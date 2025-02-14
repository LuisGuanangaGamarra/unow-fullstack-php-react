import { Form } from 'react-bootstrap';
import PropTypes from 'prop-types';

const SearchBar = ({ onSearch }) => {
    const handleChange = (e) => {
        onSearch(e.target.value);
    };

    return (
        <Form.Group controlId="search">
            <Form.Label>Buscar Empleado</Form.Label>
            <Form.Control
                type="text"
                placeholder="Buscar por nombre o apellido"
                onChange={handleChange}
            />
        </Form.Group>
    );
};

SearchBar.propTypes = {
    onSearch: PropTypes.func.isRequired,
};

export default SearchBar;
