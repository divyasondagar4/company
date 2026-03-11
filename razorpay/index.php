<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pro Subscription</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4ff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 360px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        }
        .badge {
            background: #6c63ff;
            color: white;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-block;
            margin-bottom: 20px;
        }
        h2 { font-size: 26px; color: #222; margin-bottom: 10px; }
        .price {
            font-size: 48px;
            font-weight: 700;
            color: #6c63ff;
            margin: 20px 0 5px;
        }
        .price span { font-size: 18px; color: #888; }
        .features {
            list-style: none;
            margin: 25px 0;
            text-align: left;
        }
        .features li {
            padding: 8px 0;
            color: #444;
            border-bottom: 1px solid #f0f0f0;
            font-size: 15px;
        }
        .features li::before { content: "✅ "; }
        .btn {
            background: #6c63ff;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 12px;
            font-size: 17px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        .btn:hover { background: #5a52d5; }
        #loading { display: none; color: #888; margin-top: 15px; }
    </style>
</head>
<body>

<div class="card">
    <div class="badge"> Most Popular</div>
    <h2>Pro Plan</h2>
    <p style="color:#888;">Everything you need to grow</p>

    <div class="price">₹999 <span>/ month</span></div>

    <ul class="features">
        <li>Unlimited Projects</li>
        <li>Priority Support</li>
        <li>Advanced Analytics</li>
        <li>API Access</li>
        <li>Custom Integrations</li>
    </ul>

    <button class="btn" id="buyBtn" onclick="startPayment()">
         Buy Now
    </button>
    <p id="loading"> Creating order, please wait...</p>
</div>

<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
async function startPayment() {
    document.getElementById('buyBtn').disabled = true;
    document.getElementById('loading').style.display = 'block';

    try {
        // STEP 1: Create order via backend
        const res = await fetch('create_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                plan: 'Pro Plan',
                amount: 999   // in INR
            })
        });

        const order = await res.json();

        if (!order.id) {
            alert('Failed to create order. Please try again.');
            resetButton();
            return;
        }

        // STEP 2: Open Razorpay Checkout
        const options = {
            key: order.key_id,              // from backend
            amount: order.amount,           // in paise
            currency: order.currency,
            name: "YourAppName",
            description: "Pro Plan - Monthly Subscription",
            image: "https://yourlogo.com/logo.png",  // optional
            order_id: order.id,             // Razorpay Order ID

            // STEP 3: On successful payment
            handler: function(response) {
                verifyPayment(response, order.db_order_id);
            },

            prefill: {
                name:  "Customer Name",     // you can pass logged-in user info
                email: "customer@email.com",
                contact: "9999999999"
            },

            theme: { color: "#6c63ff" },

            modal: {
                ondismiss: function() {
                    resetButton();
                }
            }
        };

        const rzp = new Razorpay(options);

        // Handle payment failure
        rzp.on('payment.failed', function(response) {
            alert('Payment failed: ' + response.error.description);
            resetButton();
        });

        rzp.open();
        document.getElementById('loading').style.display = 'none';

    } catch(err) {
        alert('Something went wrong. Please try again.');
        resetButton();
    }
}

// STEP 4: Send payment details to backend for verification
async function verifyPayment(paymentResponse, dbOrderId) {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('loading').textContent = ' Verifying payment...';

    const res = await fetch('verify_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            razorpay_order_id:   paymentResponse.razorpay_order_id,
            razorpay_payment_id: paymentResponse.razorpay_payment_id,
            razorpay_signature:  paymentResponse.razorpay_signature,
            db_order_id:         dbOrderId
        })
    });

    const result = await res.json();

    if (result.success) {
        window.location.href = 'success.php';
    } else {
        alert('Payment verification failed! Contact support with Payment ID: '
            + paymentResponse.razorpay_payment_id);
    }
}

function resetButton() {
    document.getElementById('buyBtn').disabled = false;
    document.getElementById('loading').style.display = 'none';
    document.getElementById('loading').textContent = ' Creating order, please wait...';
}
</script>

</body>
</html>