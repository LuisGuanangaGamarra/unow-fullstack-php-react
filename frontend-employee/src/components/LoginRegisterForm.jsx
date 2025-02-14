import { useState, useMemo } from 'react';
import axios from 'axios';
import { useNavigate, Link } from 'react-router-dom';
import { Form, Button, Container, Row, Col, Card, Alert } from 'react-bootstrap';
import ENDPOINTS from '../contants/endpoint.js';
import classNames from 'classnames';
import PropTypes from 'prop-types';
import wordings from './wordings.js';

const LoginRegister = ({ mode }) => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [errors, setErrors] = useState([]);
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const isLoginMode = useMemo(() => mode === 'login', [mode]);
    const wordingsPage = wordings[mode];

    const alertClassName = classNames('w-100 justify-content-center m-2', {
        'd-flex': errors.length,
        'd-none': !errors.length,
    });

    const registerLinkClassName = classNames('w-100 justify-content-center d-flex');

    const handleErrors = (error) => {
        const errorData = error?.response?.data?.error;
        const errorArray = Array.isArray(errorData) ? errorData : [errorData] || ['Ha ocurrido un error desconocido.'];
        setErrors(errorArray);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const { LOGIN, REGISTER } = ENDPOINTS;
        const url = isLoginMode ? LOGIN : REGISTER;

        try {
            const response = await axios.post(url, { email, password });
            const { token } = response.data;
            localStorage.setItem('jwt', token);
            navigate('/employees');
        } catch (error) {
            handleErrors(error);
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        if (name === 'email') {
            setEmail(value);
        } else if (name === 'password') {
            setPassword(value);
        }
    };

    return (
        <Container className="d-flex justify-content-center align-items-center min-vh-100 flex-column">
            <Row className="w-100 d-flex justify-content-center">
                <Col xs={12} md={6} lg={4}>
                    <Card className="p-4 shadow-sm">
                        <Card.Body>
                            <h3 className="text-center mb-4">{wordingsPage.title}</h3>
                            <Form onSubmit={handleSubmit}>
                                <Form.Group controlId="formEmail">
                                    <Form.Label>Correo Electrónico</Form.Label>
                                    <Form.Control
                                        type="email"
                                        placeholder="Ingresa tu correo"
                                        name="email"
                                        value={email}
                                        onChange={handleChange}
                                        required
                                    />
                                </Form.Group>

                                <Form.Group controlId="formPassword">
                                    <Form.Label>Contraseña</Form.Label>
                                    <Form.Control
                                        type="password"
                                        placeholder="Ingresa tu contraseña"
                                        name="password"
                                        value={password}
                                        onChange={handleChange}
                                        required
                                    />
                                </Form.Group>

                                <Button
                                    variant="primary"
                                    type="submit"
                                    className="w-100 mt-3"
                                    disabled={loading}
                                >
                                    {loading ? 'Cargando...' : wordingsPage.text}
                                </Button>
                            </Form>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>

            <Row className={registerLinkClassName}>
                <Col xs={12} md={6} lg={4} className="my-2 text-center">
                    {isLoginMode && <Link to="/register" reloadDocument>Registrate</Link> }
                    {!isLoginMode && <Link to="/" reloadDocument>Iniciar session</Link> }
                </Col>
            </Row>

            <Row className={alertClassName}>
                <Col xs={12} md={6} lg={4}>
                    <Alert show={errors.length} variant="danger" className="text-center">
                        {errors.map((error, index) => (
                            <p key={index} className="my-0">{error}</p>
                        ))}
                    </Alert>
                </Col>
            </Row>
        </Container>
    );
};

LoginRegister.propTypes = {
    mode: PropTypes.oneOf(['login', 'register']).isRequired,
};

export default LoginRegister;
