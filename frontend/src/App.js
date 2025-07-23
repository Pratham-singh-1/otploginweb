import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import OtpLogin from "./OtpLogin";
import Dashboard from "./Dashboard";
import ProtectedRoute from "./ProtectedRoute"; // optional

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<OtpLogin />} />
        <Route
          path="/dashboard"
          element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          }
        />
      </Routes>
    </Router>
  );
}

export default App;
