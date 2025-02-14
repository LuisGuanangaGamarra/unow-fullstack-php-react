import { useState, useEffect } from 'react';
import { Form, Button } from 'react-bootstrap';
import PropTypes from 'prop-types';
import { format, parseISO } from 'date-fns';

const EmployeeForm = ({ selectedEmployee, positions, onClose, onSave }) => {
    const [formData, setFormData] = useState({
        first_name: '',
        last_name: '',
        position: '',
        birth_date: '',
        email: '',
    });

    useEffect(() => {
        if (selectedEmployee) {
            const formattedDate = selectedEmployee.birth_date
                ? format(parseISO(selectedEmployee.birth_date), 'yyyy-MM-dd')
                : '';

            setFormData({
                first_name: selectedEmployee.first_name,
                last_name: selectedEmployee.last_name,
                position: selectedEmployee.position,
                birth_date: formattedDate,
                id: selectedEmployee.id,
            });
        }
    }, [selectedEmployee]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = () => {
        const formattedBirthDate = formData.birth_date
            ? format(new Date(formData.birth_date), 'yyyy-MM-dd')
            : '';

        const updatedData = {
            ...formData,
            birth_date: formattedBirthDate,
        };

        onSave(updatedData);
    };

    return (
        <Form>
            <Form.Group controlId="first_name">
                <Form.Label>Nombre</Form.Label>
                <Form.Control
                    type="text"
                    placeholder="Ingrese el nombre"
                    name="first_name"
                    value={formData.first_name}
                    onChange={handleChange}
                />
            </Form.Group>

            <Form.Group controlId="last_name">
                <Form.Label>Apellido</Form.Label>
                <Form.Control
                    type="text"
                    placeholder="Ingrese el apellido"
                    name="last_name"
                    value={formData.last_name}
                    onChange={handleChange}
                />
            </Form.Group>

            <Form.Group controlId="position">
                <Form.Label>Posición</Form.Label>
                <Form.Control
                    as="select"
                    name="position"
                    value={formData.position}
                    onChange={handleChange}
                >
                    <option value="">Seleccione una posición</option>
                    {positions.map((position, index) => (
                        <option key={index} value={position}>
                            {position}
                        </option>
                    ))}
                </Form.Control>
            </Form.Group>

            <Form.Group controlId="birth_date">
                <Form.Label>Fecha de nacimiento</Form.Label>
                <Form.Control
                    type="date"
                    name="birth_date"
                    value={formData.birth_date}
                    onChange={handleChange}
                    disabled={!!selectedEmployee?.birth_date}
                />
            </Form.Group>

            {!selectedEmployee && (
                <Form.Group controlId="email">
                    <Form.Label>Email</Form.Label>
                    <Form.Control
                        type="email"
                        placeholder="Ingrese el email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                    />
                </Form.Group>
            )}

            <Button variant="secondary" className="my-3" onClick={onClose}>
                Cerrar
            </Button>
            <Button variant="primary" className="mx-3 my-3" onClick={handleSubmit}>
                Guardar
            </Button>
        </Form>
    );
};

EmployeeForm.propTypes = {
    selectedEmployee: PropTypes.shape({
        first_name: PropTypes.string,
        last_name: PropTypes.string,
        position: PropTypes.string,
        birth_date: PropTypes.string,
        id: PropTypes.number,
    }),
    onClose: PropTypes.func,
    onSave: PropTypes.func,
    positions: PropTypes.arrayOf(PropTypes.string).isRequired,
};

export default EmployeeForm;
