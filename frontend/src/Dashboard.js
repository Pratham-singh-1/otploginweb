import React from "react";
import "./Dashboard.css";

export default function Dashboard() {
  const mobile = localStorage.getItem("user");

  return (
    <div className="dashboard-container">
      <div className="dashboard-card">
        <h1>Welcome 🎉</h1>
        <p className="dashboard-text">
          Hello <strong>{mobile}</strong>, you are successfully logged in using
          OTP.
        </p>

        <div className="dashboard-buttons">
          <button
            className="dashboard-btn logout"
            onClick={() => {
              localStorage.removeItem("user");
              window.location.href = "/";
            }}
          >
            Logout
          </button>
          <button className="dashboard-btn success" disabled>
            Access Granted
          </button>
        </div>
      </div>
    </div>
  );
}
