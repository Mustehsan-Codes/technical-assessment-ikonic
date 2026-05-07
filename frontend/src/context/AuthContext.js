import React, { createContext, useState, useContext } from 'react';
import api from '../services/api';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(JSON.parse(localStorage.getItem('user')));
  const [token, setToken] = useState(localStorage.getItem('token'));

  const login = async (email, password) => {
    try {
      const response = await api.post('/login', { email, password });
      const { user: userData, token: authToken } = response.data;

      localStorage.setItem('token', authToken);
      localStorage.setItem('user', JSON.stringify(userData));
      setUser(userData);
      setToken(authToken);

      return userData;
    } catch (error) {
      console.error('Login error', error.response?.data);
      throw error;
    }
  };

  const register = async (name, email, password, password_confirmation) => {
    try {
      const response = await api.post('/register', {
        name,
        email,
        password,
        password_confirmation,
      });
      const { user: userData, token: authToken } = response.data;

      localStorage.setItem('token', authToken);
      localStorage.setItem('user', JSON.stringify(userData));
      setUser(userData);
      setToken(authToken);

      return userData;
    } catch (error) {
      console.error('Registration error', error.response?.data);
      throw error;
    }
  };

  const logout = async () => {
    await api.post('/logout');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setUser(null);
    setToken(null);
  };

  return (
    <AuthContext.Provider value={{ user, token, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
