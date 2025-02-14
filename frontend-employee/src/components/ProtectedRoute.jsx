import { useNavigate } from 'react-router-dom';
import PropTypes from 'prop-types';
import { useEffect } from 'react';

const ProtectedRoute = ({ children }) => {
    const navigate = useNavigate();


    useEffect(() => {
        const token = localStorage.getItem('jwt');
        if (!token) {
            return navigate('/');
        }
    }, [ navigate ]);

    return children;
}

ProtectedRoute.propTypes = {
    children: PropTypes.node.isRequired,
};

export default ProtectedRoute;
