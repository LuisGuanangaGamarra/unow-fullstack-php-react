import { Button, Table } from 'react-bootstrap';
import PropTypes from "prop-types";

const EmployeeTable = ({ employees, onEdit, onDelete }) => {
    return (
        <Table striped bordered hover>
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Posición</th>
                <th>Fecha de nacimiento</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            {employees.map((employee) => (
                <tr key={employee.id}>
                    <td>{employee.first_name}</td>
                    <td>{employee.last_name}</td>
                    <td>{employee.position}</td>
                    <td>{employee.birth_date}</td>
                    <td>
                        <Button variant="primary" className="mx-3" onClick={() => onEdit(employee)}>Editar</Button>
                        <Button variant="danger" onClick={() => onDelete(employee.id)}>Eliminar</Button>
                    </td>
                </tr>
            ))}
            </tbody>
        </Table>
    );
};

EmployeeTable.propTypes = {
    employees: PropTypes.arrayOf(PropTypes.shape({})).isRequired,
    onEdit: PropTypes.func,
    onDelete: PropTypes.func,
};

export default EmployeeTable;
