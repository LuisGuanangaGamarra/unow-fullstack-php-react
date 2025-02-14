const ENDPOINTS = {
    LOGIN: 'http://localhost:8000/api/users/login',
    REGISTER: 'http://localhost:8000/api/users/register',
    EMPLOYEES: 'http://localhost:8002/api/employees',
    EMPLOYEES_DELETE_OR_UPDATE: (id) => `http://localhost:8002/api/employees/${id}`,
    POSITIONS_API: '/puesto/api/positions',
};

export default ENDPOINTS;