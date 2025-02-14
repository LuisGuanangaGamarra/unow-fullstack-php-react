import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import LoginRegisterForm from './components/LoginRegisterForm';
import EmployeeList from './components/EmployeeList';
import ProtectedRoute from './components/ProtectedRoute';

function App() {
    return (
        <Router>
            <Routes>
                <Route path="/" element={<LoginRegisterForm mode="login" />} />
                <Route path="/register" element={<LoginRegisterForm mode="register" />} />
                <Route path="/employees" element={<ProtectedRoute><EmployeeList /></ProtectedRoute>} />
            </Routes>
        </Router>
    );
}

export default App;
