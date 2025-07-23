import React, { useState } from "react";
import "./OtpLogin.css";

function OtpLogin() {
  const [mobile, setMobile] = useState("");
  const [otp, setOtp] = useState("");
  const [otpSent, setOtpSent] = useState(false);
  const [message, setMessage] = useState("");
  const [shake, setShake] = useState(false);

  const isValidMobile = (number) => /^[6-9]\d{9}$/.test(number);
  const isValidOtp = (code) => /^\d{6}$/.test(code);

  const sendOtp = async () => {
    if (!isValidMobile(mobile)) {
      setMessage("Please enter a valid 10-digit Indian mobile number.");
      return;
    }

    try {
      const res = await fetch(
        "http://localhost/otplogin/backend/controllers/send_otp.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ mobile }),
        }
      );

      const data = await res.json();
      console.log("Send OTP response:", data); // Dev debug

      if (data.status === "success") {
        setOtpSent(true);

        setMessage("OTP sent to your number.");
      } else {
        setMessage(data.message || "Failed to send OTP.");
      }
    } catch (err) {
      console.error("OTP Send Error:", err);
      setMessage("Network error. Try again.");
    }
  };

  const verifyOtp = async () => {
    if (!isValidOtp(otp)) {
      setMessage("Please enter a valid 6-digit OTP.");
      return;
    }

    try {
      const res = await fetch(
        "http://localhost/otplogin/backend/controllers/verify_otp.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ mobile, otp }),
        }
      );

      const data = await res.json();
      console.log("Verify OTP response:", data); // Dev debug

      if (data.status === "success") {
        setMessage("OTP Verified! Redirecting...");
        localStorage.setItem("user", mobile);
        setTimeout(() => (window.location.href = "/dashboard"), 1500);
      } else {
        setMessage(data.message || "Verification failed.");
        setShake(true);
        setTimeout(() => setShake(false), 400);
      }
    } catch (err) {
      console.error("OTP Verification Error:", err);
      setMessage("Network error. Try again.");
    }
  };

  return (
    <div className="otp-container">
      <div className={`otp-box ${shake ? "shake" : ""}`}>
        <h2 className="otp-heading">OTP Login</h2>

        {!otpSent ? (
          <>
            <input
              type="text"
              placeholder="Enter Mobile Number"
              value={mobile}
              onChange={(e) => setMobile(e.target.value)}
              className="otp-input"
            />
            <button onClick={sendOtp} className="otp-button send">
              Send OTP
            </button>
          </>
        ) : (
          <>
            <input
              type="text"
              placeholder="Enter OTP"
              value={otp}
              onChange={(e) => setOtp(e.target.value)}
              className="otp-input"
            />
            <button onClick={verifyOtp} className="otp-button verify">
              Verify OTP
            </button>
          </>
        )}

        {message && (
          <div
            className={`otp-message ${
              message.toLowerCase().includes("fail")
                ? "otp-fail"
                : "otp-success"
            }`}
          >
            {message}
          </div>
        )}
      </div>
    </div>
  );
}

export default OtpLogin;
