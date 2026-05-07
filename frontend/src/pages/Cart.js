import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';

const Cart = () => {
  const { cartItems, cartTotal, loading, fetchCart, updateCartItem, removeFromCart } = useCart();
  const { user } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (user) {
      console.log('Cart page mounted, fetching cart data');
      fetchCart();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user]);

  const handleQuantityChange = (itemId, newQuantity) => {
    if (newQuantity < 1) return;
    updateCartItem(itemId, newQuantity);
  };

  const handleRemove = (itemId) => {
    removeFromCart(itemId);
  };

  const handleCheckout = () => {
    navigate('/checkout');
  };

  if (!user) {
    return (
      <div style={styles.container}>
        <p>Please login to view your cart.</p>
      </div>
    );
  }

  if (loading) {
    return (
      <div style={styles.container}>
        <p>Loading cart...</p>
      </div>
    );
  }

  return (
    <div style={styles.container}>
      <h1 style={styles.heading}>Shopping Cart</h1>

      {cartItems.length === 0 ? (
        <p style={styles.empty}>Your cart is empty.</p>
      ) : (
        <>
          <div style={styles.itemsList}>
            {cartItems.map((item) => (
              <div key={item.id} style={styles.item}>
                <div style={styles.itemInfo}>
                  <h3 style={styles.itemName}>
                    {item.product ? item.product.name : 'Product'}
                  </h3>
                  <p style={styles.itemPrice}>${item.price} each</p>
                </div>
                <div style={styles.itemActions}>
                  <button
                    onClick={() => handleQuantityChange(item.id, item.quantity - 1)}
                    style={styles.qtyBtn}
                  >
                    -
                  </button>
                  <span style={styles.qty}>{item.quantity}</span>
                  <button
                    onClick={() => handleQuantityChange(item.id, item.quantity + 1)}
                    style={styles.qtyBtn}
                  >
                    +
                  </button>
                  <span style={styles.subtotal}>
                    ${(item.price * item.quantity).toFixed(2)}
                  </span>
                  <button
                    onClick={() => handleRemove(item.id)}
                    style={styles.removeBtn}
                  >
                    Remove
                  </button>
                </div>
              </div>
            ))}
          </div>
          <div style={styles.totalSection}>
            <h2>Total: ${Number(cartTotal).toFixed(2)}</h2>
            <button onClick={handleCheckout} style={styles.checkoutBtn}>
              Proceed to Checkout
            </button>
          </div>
        </>
      )}
    </div>
  );
};

const styles = {
  container: { maxWidth: '800px', margin: '0 auto', padding: '24px' },
  heading: { color: '#2c3e50', marginBottom: '20px' },
  empty: { textAlign: 'center', color: '#999', marginTop: '40px' },
  itemsList: { marginBottom: '24px' },
  item: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '16px',
    background: '#fff',
    borderRadius: '8px',
    boxShadow: '0 1px 4px rgba(0,0,0,0.1)',
    marginBottom: '12px',
  },
  itemInfo: {},
  itemName: { margin: 0, color: '#2c3e50', fontSize: '16px' },
  itemPrice: { color: '#666', fontSize: '14px', margin: '4px 0 0' },
  itemActions: { display: 'flex', alignItems: 'center', gap: '12px' },
  qtyBtn: {
    width: '32px',
    height: '32px',
    border: '1px solid #ddd',
    borderRadius: '4px',
    background: '#f8f9fa',
    cursor: 'pointer',
    fontSize: '16px',
  },
  qty: { fontSize: '16px', fontWeight: 'bold', minWidth: '20px', textAlign: 'center' },
  subtotal: { fontSize: '16px', fontWeight: 'bold', color: '#27ae60', minWidth: '80px' },
  removeBtn: {
    background: '#e74c3c',
    color: '#fff',
    border: 'none',
    padding: '6px 12px',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '13px',
  },
  totalSection: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '20px',
    background: '#fff',
    borderRadius: '8px',
    boxShadow: '0 1px 4px rgba(0,0,0,0.1)',
  },
  checkoutBtn: {
    padding: '12px 32px',
    backgroundColor: '#27ae60',
    color: '#fff',
    border: 'none',
    borderRadius: '4px',
    fontSize: '16px',
    cursor: 'pointer',
  },
};

export default Cart;
