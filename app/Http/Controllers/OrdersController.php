<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\Events;
use App\Models\Orders;
use App\Models\Tickets;
use Barryvdh\DomPDF\PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrdersController extends Controller
{
    public function getOrders()
    {
        $orders = Orders::all();

        return response()->json(['data' => $orders], 200);
    }

    public function createOrders(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user || $user->role->name !== 'user') {
                return response()->json([
                    'message' => 'Only users can place orders'
                ], 403);
            }

            $validated = $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $ticket = Tickets::with('event', 'type_ticket')->findOrFail($validated['ticket_id']);

            if (!$ticket) {
                return response()->json([
                    'message' => 'Ticket not found'
                ], 404);
            }

            if ($validated['quantity'] > $ticket->quantity) {
                return response()->json([
                    'message' => 'Not enough tickets available',
                    'available_quantity' => $ticket->quantity
                ], 400);
            }

            $orderList = array_map(fn() => Orders::create([
                'user_id' => $user->id,
                'ticket_id' => $validated['ticket_id'],
                'is_canceled' => false
            ]), range(1, $validated['quantity']));

            $ticket->decrement('quantity', $validated['quantity']);

            $updatedTicket = Tickets::find($ticket->id);

            $orderDetails = [
                'event_name' => $updatedTicket->event->title,
                'date' => $updatedTicket->event->date,
                'time' => $updatedTicket->event->time,
                'location' => $updatedTicket->event->location,
                'ticket_name' => $updatedTicket->name,
                'price' => $updatedTicket->price,
                'type_ticket' => $updatedTicket->type_ticket->name,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
            ];

            $pdf = app(PDF::class);

            $pdf->loadView('emails.ticket', $orderDetails);

            // Store file to local driver
            Storage::disk('local')->put('/tickets/ticket.pdf', $pdf->output());

            // Get file path
            $TicketPath = Storage::path('/tickets/ticket.pdf');

            // Upload Pdf to cloudinary
            $ticketURL = cloudinary()->upload($TicketPath, ['folder' => 'evenly-tickets', 'access_mode' => 'public', 'verify' => false])->getSecurePath();

            Mail::to($user)->later(now()->addMinutes(2), new OrderMail($user, $ticketURL));

            return response()->json([
                'message' => 'Order successfully made. An email will be sent to you with your ticket',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrdersByUser(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user || $user->role->name !== 'user') {
                return response()->json([
                    'message' => 'Only users can place orders'
                ], 403);
            }

            $order = Orders::where('user_id', $user->id)->get();

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json(['data' => $order], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  500);
        }
    }

    public function getOrdersOnOrganizerEvents(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role->name !== 'organizer') {
            return response()->json([
                'message' => 'Only organizers can view events.',
            ], 403);
        }

        $events = Events::where('user_id', $user->id)->get();

        $ordersData = $events->map(function ($event) {
            $ticketIds = Tickets::where('event_id', $event->id)->pluck('id');

            $orders = Orders::whereIn('ticket_id', $ticketIds)->get();

            return $orders;
        })->flatten();

        return response()->json($ordersData, 200);
    }

    public function cancelOrders(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->role->name !== 'user') {
            return response()->json([
                'message' => 'Only users can place orders'
            ], 403);
        }

        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->user_id !== $user->id) {
            return response()->json([
                'message' => 'You can only cancel your own orders'
            ], 403);
        }

        if ($order->is_canceled) {
            return response()->json([
                'message' => 'This order is already canceled'
            ], 400);
        }

        $order->is_canceled = true;
        $order->save();

        $ticket = $order->ticket;

        $ticket->quantity += $order->quantity;
        $ticket->save();

        return response()->json([
            'message' => 'Order canceled successfully',
            'data' => $order
        ], 200);
    }

    public function destroyOrders($id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || $user->role->name !== 'user') {
            return response()->json([
                'message' => 'Only users can place orders'
            ], 403);
        }

        $order = Orders::findOrFail($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ], 200);
    }
}
