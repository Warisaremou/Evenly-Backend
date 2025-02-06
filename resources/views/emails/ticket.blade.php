<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ticket</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Geist:wght@100..900&display=swap");

    :root {
      --bg-color: #f1f1f1;
      --primary-color: #203098;
    }

    body {
      font-family: "Bricolage Grotesque", cursive;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 15px;
      background-color: var(--bg-color);
    }

    .ticket {
      display: grid;
      grid-template-columns: 1fr 2fr 1fr;
      width: 100%;
      max-width: 50rem;
      background: var(--bg-color);
      border: 1px solid var(--primary-color);
      color: var(--primary-color);
    }

    .qr-code {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 10px;
    }

    .qr-code img {
      width: 150px;
      height: 150px;
    }

    .info {
      padding: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      border-width: 0 1px;
      border-style: solid;
      border-color: var(--primary-color);
    }

    .info h2 {
      font-weight: 600;
    }

    .vertical-divider {
      width: 10rem;
      height: 1px;
      background: var(--primary-color);
    }

    .info .date_time {
      font-weight: 600;
      font-size: 1rem;
      line-height: 10px;
      width: 100%;
      text-align: center;
    }

    .name,
    .publish_date {
      font-family: "Geist", sans-serif;
      font-weight: 500;
    }

    .name {
      font-size: 16px;
    }

    .details {
      padding: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
    }

    .details-header p {
      text-align: center;
      font-size: 20px;
      font-weight: 600;
      margin: 12px 0;
    }

    .publish_date {
      text-align: center;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="ticket">
    <div class="qr-code">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=ART_CREATIVE_FESTIVAL" alt="QR Code" />
    </div>

    <div class="info">
      <h2>ART CREATIVE FESTIVAL</h2>
      <div class="vertical-divider"></div>
      <div class="date_time">
        <p>3 PM</p>
        <p>25 SEPTEMBER 2024</p>
      </div>
      <div class="vertical-divider"></div>
      <p class="name">Kimberly Nguyen</p>
    </div>

    <div class="details">
      <div class="details-header">
        <p>123 Anywhere St. Any City.</p>
        <p>PRICE: 0€</p>
      </div>
      <p class="publish_date">Purchased on 15th September 2024</p>
    </div>
  </div>
</body>

</html>