import React, { useState, useEffect } from 'react';
import api from '../services/api';
import { useAuth } from '../context/AuthContext';

const Orders = () => {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const { user } = useAuth();

  const [error, setError] = useState(null);

  useEffect(() => {
    if (user) {
      console.log('Orders page mounted, fetching user orders');
      fetchOrders();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user]);

  const fetchOrders = async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await api.get('/orders');
      console.log('Orders fetched', response.data);
      setOrders(response.data);
    } catch (err) {
      console.error('Failed to fetch orders', err);
      setError('Could not load orders. Please try again later.');
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'paid': return '#27ae60';
      case 'pending': return '#f39c12';
      case 'failed': return '#e74c3c';
      default: return '#95a5a6';
    }
  };

  if (!user) {
    return (
      <div style={styles.container}>
        <p>Please login to view your orders.</p>
      </div>
    );
  }

  if (loading) {
    return (
      <div style={styles.container}>
        <p>Loading orders...</p>
      </div>
    );
  }

  return (
    <div style={styles.container}>
      <h1 style={styles.heading}>My Orders</h1>

      {error && <div style={styles.error}>{error}</div>}

      {orders.length === 0 ? (
        <p style={styles.empty}>No orders yet.</p>
      ) : (
        <div>
          {orders.map((order) => (
            <div key={order.id} style={styles.orderCard}>
              <div style={styles.orderHeader}>
                <div>
                  <h3 style={styles.orderId}>Order #{order.id}</h3>
                  <p style={styles.orderDate}>
                    {new Date(order.created_at).toLocaleDateString()}
                  </p>
                </div>
                <div style={styles.orderMeta}>
                  <span
                    style={{
                      ...styles.status,
                      backgroundColor: getStatusColor(order.status),
                    }}
                  >
                    {order.status}
                  </span>
                  <span style={styles.orderTotal}>
                    ${Number(order.total).toFixed(2)}
                  </span>
                </div>
              </div>
              <div style={styles.orderItems}>
                {order.items && order.items.map((item) => (
                  <div key={item.id} style={styles.orderItem}>
                    <span>{item.product_name} x{item.quantity}</span>
                    <span>${(item.price * item.quantity).toFixed(2)}</span>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

const styles = {
  container: { maxWidth: '800px', margin: '0 auto', padding: '24px' },
  heading: { color: '#2c3e50', marginBottom: '20px' },
  empty: { textAlign: 'center', color: '#999', marginTop: '40px' },
  orderCard: {
    background: '#fff',
    borderRadius: '8px',
    boxShadow: '0 1px 4px rgba(0,0,0,0.1)',
    marginBottom: '16px',
    overflow: 'hidden',
  },
  orderHeader: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '16px 20px',
    borderBottom: '1px solid #eee',
  },
  orderId: { margin: 0, color: '#2c3e50' },
  orderDate: { color: '#999', fontSize: '13px', margin: '4px 0 0' },
  orderMeta: { display: 'flex', alignItems: 'center', gap: '12px' },
  status: {
    color: '#fff',
    padding: '4px 12px',
    borderRadius: '12px',
    fontSize: '12px',
    textTransform: 'uppercase',
  },
  orderTotal: { fontSize: '18px', fontWeight: 'bold', color: '#2c3e50' },
  orderItems: { padding: '12px 20px' },
  orderItem: {
    display: 'flex',
    justifyContent: 'space-between',
    padding: '6px 0',
    fontSize: '14px',
    color: '#555',
  },
  error: {
    background: '#fee',
    color: '#c00',
    padding: '12px',
    borderRadius: '4px',
    marginBottom: '20px',
    textAlign: 'center',
  },
};

export default Orders;
