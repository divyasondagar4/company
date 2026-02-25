import { useState } from "react";

export default function Subscribe() {
  const [selectedPlan, setSelectedPlan] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const activatePlan = async () => {
    if (!selectedPlan) {
      alert("Please select a subscription plan.");
      return;
    }

    setLoading(true);

    try {
      const formData = new FormData();
      formData.append("plan", selectedPlan);
      formData.append("user_id", "1"); // Same user ID everywhere

      const response = await fetch(
        "http://localhost/vibe_tech_labs/php-backend/activate-subscription.php",
        {
          method: "POST",
          body: formData,
        }
      );

      const data = await response.json();

      if (data.status === "success") {
        alert("Subscription Activated Successfully!");

        // 🔥 Directly download PDF after activation
        if (data.download_url) {
          window.location.href = data.download_url;
        } else {
          // fallback: check subscription and download
          const check = await fetch(
            "http://localhost/vibe_tech_labs/php-backend/check-subscription.php"
          );
          const checkData = await check.json();

          if (checkData.status === "active") {
            window.location.href = checkData.download_url;
          }
        }

      } else {
        alert("Subscription failed.");
      }

    } catch (error) {
      alert("Server error.");
    }

    setLoading(false);
  };

  return (
    <div className="container py-5 text-center">
      <h2 className="mb-4">Choose Your Plan</h2>

      <div className="row justify-content-center gap-4">

        {/* 1 Month */}
        <div
          className={`card col-md-4 p-4 text-center ${
            selectedPlan === "month" ? "border border-primary" : ""
          }`}
          onClick={() => setSelectedPlan("month")}
          style={{ cursor: "pointer" }}
        >
          <h4>1 Month</h4>
          <h2>₹99</h2>
        </div>

        {/* 1 Year */}
        <div
          className={`card col-md-4 p-4 text-center ${
            selectedPlan === "year" ? "border border-success" : ""
          }`}
          onClick={() => setSelectedPlan("year")}
          style={{ cursor: "pointer" }}
        >
          <h4>1 Year</h4>
          <h2>₹999</h2>
        </div>
      </div>

      <button
        className="btn btn-dark mt-4"
        onClick={activatePlan}
        disabled={loading}
      >
        {loading ? "Processing..." : "Subscribe & Download"}
      </button>
    </div>
  );
}