import { useState, useEffect } from 'react';
import axios from 'axios';
import { Modal, Button } from 'react-bootstrap';
import ENDPOINTS from '../contants/endpoint.js';
import EmployeeTable from './EmployeeTable.jsx';
import EmployeeForm from './EmployeeForm.jsx';
import SearchBar from './SearchBar';
import { useNavigate } from 'react-router-dom';

const { EMPLOYEES, POSITIONS_API, EMPLOYEES_DELETE_OR_UPDATE } = ENDPOINTS;

const EmployeeList = () => {
    const navigate = useNavigate();
    const [employees, setEmployees] = useState([]);
    const [filteredEmployees, setFilteredEmployees] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [selectedEmployee, setSelectedEmployee] = useState(null);
    const [positions, setPositions] = useState([]);

    useEffect(() => {
        const fetchEmployees = async () => {
            const token = localStorage.getItem('jwt');
            const response = await axios.get(EMPLOYEES, {
                headers: { Authorization: `Bearer ${token}` },
            });
            setEmployees(response.data);
            setFilteredEmployees(response.data);
        };

        const fetchPositions = async () => {
            const response = await axios.get(POSITIONS_API);
            setPositions(response?.data?.positions ?? []);
        };

        fetchEmployees();
        fetchPositions();
    }, []);

    const handleDelete = async (id) => {
        const token = localStorage.getItem('jwt');
        await axios.delete(EMPLOYEES_DELETE_OR_UPDATE(id), {
            headers: { Authorization: `Bearer ${token}` },
        });
        setEmployees(prev => prev.filter(emp => emp.id !== id));
        setFilteredEmployees(prev => prev.filter(emp => emp.id !== id));
    };

    const handleEdit = (employee) => {
        setSelectedEmployee(employee);
        setShowModal(true);
    };

    const handleSearch = (searchTerm) => {
        if (searchTerm === '') {
            setFilteredEmployees(employees);
        } else {
            setFilteredEmployees(employees.filter(employee =>
                employee.first_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                employee.last_name.toLowerCase().includes(searchTerm.toLowerCase())
            ));
        }
    };

    const handleCloseModal = () => setShowModal(false);

    const handleEditEmployee = async (employee) => {
        const token = localStorage.getItem('jwt');
        await axios.put(EMPLOYEES_DELETE_OR_UPDATE(employee.id), {
            first_name: employee.first_name,
            last_name: employee.last_name,
            position: employee.position,
        }, {
            headers: { Authorization: `Bearer ${token}` },
        });

        const response = await axios.get(EMPLOYEES, {
            headers: { Authorization: `Bearer ${token}` },
        });
        setEmployees(response.data);
        setFilteredEmployees(response.data);
        setSelectedEmployee(null);

        handleCloseModal();
    }

    const handleAddEmployee = async (employee) => {
        handleCloseModal();
        const token = localStorage.getItem('jwt');

        await axios.post(EMPLOYEES, {
            first_name: employee.first_name,
            last_name: employee.last_name,
            position: employee.position,
            birth_date: employee.birth_date,
            email: employee.email,
        }, {
            headers: { Authorization: `Bearer ${token}` },
        });

        const response = await axios.get(EMPLOYEES, {
            headers: { Authorization: `Bearer ${token}` },
        });
        setEmployees(response.data);
        setFilteredEmployees(response.data);
    };

    const handleOpenAddModal = () => {
        setSelectedEmployee(null);
        setShowModal(true);
    };

    const handleLogut = () => {
        localStorage.removeItem('jwt');
        navigate('/');
    };

    return (
        <div className="container mt-4">
            <h2>Lista de empleados</h2>
            <SearchBar onSearch={handleSearch} />

            <Button variant="success" className="my-3" onClick={handleOpenAddModal}>
                Agregar Nuevo Empleado
            </Button>

            <Button variant="secondary" className="my-3 mx-3" onClick={handleLogut}>
                Salir del sistema
            </Button>

            <EmployeeTable
                employees={filteredEmployees}
                onEdit={handleEdit}
                onDelete={handleDelete}
            />

            <Modal show={showModal} onHide={handleCloseModal}>
                <Modal.Header closeButton>
                    <Modal.Title>{selectedEmployee ? 'Editar Empleado' : 'Agregar Nuevo Empleado'}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <EmployeeForm
                        selectedEmployee={selectedEmployee}
                        positions={positions}
                        onClose={handleCloseModal}
                        onSave={selectedEmployee ? handleEditEmployee : handleAddEmployee}
                    />
                </Modal.Body>
            </Modal>
        </div>
    );
};

export default EmployeeList;
