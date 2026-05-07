import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useCart } from '../context/CartContext';

const Checkout = () => {
  const [shippingAddress, setShippingAddress] = useState('');
  const [billingAddress, setBillingAddress] = useState('');
  const [paymentMethod, setPaymentMethod] = useState('credit_card');
  const [processing, setProcessing] = useState(false);
  const [orderComplete, setOrderComplete] = useState(false);
  const [order, setOrder] = useState(null);
  const [error, setError] = useState(null);
  const { cartItems, cartTotal, clearCart } = useCart();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setProcessing(true);
    setError(null);

    try {
      console.log('Initiating checkout process');
      const checkoutResponse = await api.post('/checkout', {
        shipping_address: shippingAddress,
        billing_address: billingAddress,
        payment_method: paymentMethod,
      });

      const orderData = checkoutResponse.data.order;
      setOrder(orderData);
      console.log('Order created', orderData.id);

      console.log('Processing payment for order', orderData.id);
      // FIX: Changed from GET to POST for payment processing (sensitive operation with side effects)
      const paymentResponse = await api.post(`/checkout/pay/${orderData.id}`);

      // FIX: Added explicit check for payment success.
      // Previously, it would clear the cart and show success even if payment failed.
      if (paymentResponse.data.message === 'Payment successful') {
        console.log('Payment successful');
        setOrderComplete(true);
        clearCart();
      } else {
        console.error('Payment failed', paymentResponse.data.message);
        setError(`Payment failed: ${paymentResponse.data.message || 'Please try again.'}`);
      }
    } catch (err) {
      console.error('Checkout error', err);
      setError(err.response?.data?.message || 'An error occurred during checkout. Please try again.');
    } finally {
      setProcessing(false);
    }
  };

  if (orderComplete && order) {
    return (
      <div style={styles.container}>
        <div style={styles.successCard}>
          <h2 style={styles.successTitle}>Order Confirmed!</h2>
          <p>Your order #{order.id} has been placed successfully.</p>
          <p>Total: ${Number(order.total).toFixed(2)}</p>
          <p>Status: Paid</p>
          <button
            onClick={() => navigate('/orders')}
            style={styles.viewOrdersBtn}
          >
            View Orders
          </button>
        </div>
      </div>
    );
  }

  return (
    <div style={styles.container}>
      <h1 style={styles.heading}>Checkout</h1>

      {error && (
        <div style={styles.errorAlert}>
          {error}
        </div>
      )}

      {cartItems.length === 0 && !processing ? (
        <p>Your cart is empty. <a href="/products">Continue shopping</a></p>
      ) : (
        <div style={styles.layout}>
          <div style={styles.formSection}>
            <form onSubmit={handleSubmit}>
              <h3>Shipping Information</h3>
              <div style={styles.formGroup}>
                <label>Shipping Address</label>
                <textarea
                  value={shippingAddress}
                  onChange={(e) => setShippingAddress(e.target.value)}
                  style={styles.textarea}
                  required
                  rows={3}
                />
              </div>
              <div style={styles.formGroup}>
                <label>Billing Address</label>
                <textarea
                  value={billingAddress}
                  onChange={(e) => setBillingAddress(e.target.value)}
                  style={styles.textarea}
                  rows={3}
                  placeholder="Same as shipping if left empty"
                />
              </div>

              <h3>Payment Method</h3>
              <div style={styles.formGroup}>
                <select
                  value={paymentMethod}
                  onChange={(e) => setPaymentMethod(e.target.value)}
                  style={styles.select}
                >
                  <option value="credit_card">Credit Card</option>
                  <option value="debit_card">Debit Card</option>
                  <option value="paypal">PayPal</option>
                </select>
              </div>

              <button
                type="submit"
                style={styles.payBtn}
                disabled={processing}
              >
                {processing ? 'Processing...' : `Pay $${Number(cartTotal).toFixed(2)}`}
              </button>
            </form>
          </div>

          <div style={styles.summarySection}>
            <h3>Order Summary</h3>
            {cartItems.map((item) => (
              <div key={item.id} style={styles.summaryItem}>
                <span>{item.product?.name || 'Item'} x{item.quantity}</span>
                <span>${(item.price * item.quantity).toFixed(2)}</span>
              </div>
            ))}
            <div style={styles.summaryTotal}>
              <strong>Total</strong>
              <strong>${Number(cartTotal).toFixed(2)}</strong>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

const styles = {
  container: { maxWidth: '900px', margin: '0 auto', padding: '24px' },
  heading: { color: '#2c3e50', marginBottom: '20px' },
  layout: { display: 'flex', gap: '24px', flexWrap: 'wrap' },
  formSection: { flex: '1', minWidth: '300px' },
  summarySection: {
    width: '300px',
    background: '#fff',
    padding: '20px',
    borderRadius: '8px',
    boxShadow: '0 1px 4px rgba(0,0,0,0.1)',
    alignSelf: 'flex-start',
  },
  formGroup: { marginBottom: '16px' },
  textarea: {
    width: '100%',
    padding: '10px',
    border: '1px solid #ddd',
    borderRadius: '4px',
    fontSize: '14px',
    marginTop: '4px',
    boxSizing: 'border-box',
    fontFamily: 'inherit',
  },
  select: {
    width: '100%',
    padding: '10px',
    borderRadius: '4px',
    border: '1px solid #ddd',
    fontSize: '14px',
  },
  payBtn: {
    width: '100%',
    padding: '14px',
    backgroundColor: '#27ae60',
    color: '#fff',
    border: 'none',
    borderRadius: '4px',
    fontSize: '18px',
    cursor: 'pointer',
    marginTop: '16px',
  },
  summaryItem: {
    display: 'flex',
    justifyContent: 'space-between',
    padding: '8px 0',
    borderBottom: '1px solid #eee',
    fontSize: '14px',
  },
  summaryTotal: {
    display: 'flex',
    justifyContent: 'space-between',
    padding: '12px 0',
    fontSize: '18px',
    borderTop: '2px solid #2c3e50',
    marginTop: '8px',
  },
  successCard: {
    textAlign: 'center',
    padding: '40px',
    background: '#fff',
    borderRadius: '8px',
    boxShadow: '0 2px 10px rgba(0,0,0,0.1)',
  },
  successTitle: { color: '#27ae60', fontSize: '28px' },
  viewOrdersBtn: {
    padding: '12px 24px',
    backgroundColor: '#3498db',
    color: '#fff',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
    marginTop: '16px',
  },
  errorAlert: {
    padding: '12px',
    backgroundColor: '#f8d7da',
    color: '#721c24',
    border: '1px solid #f5c6cb',
    borderRadius: '4px',
    marginBottom: '20px',
  },
};

export default Checkout;
