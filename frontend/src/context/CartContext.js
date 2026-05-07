import React, { createContext, useState, useContext } from 'react';
import api from '../services/api';

const CartContext = createContext(null);

export const CartProvider = ({ children }) => {
  const [cartItems, setCartItems] = useState([]);
  const [cartTotal, setCartTotal] = useState(0);
  const [loading, setLoading] = useState(false);

  const fetchCart = async () => {
    setLoading(true);
    try {
      const response = await api.get('/cart');
      console.log('Cart fetched', response.data);
      setCartItems(response.data.items || []);
      setCartTotal(response.data.total || 0);
    } catch (error) {
      console.error('Failed to fetch cart', error);
    } finally {
      setLoading(false);
    }
  };

  const addToCart = async (productId, quantity = 1) => {
    try {
      const response = await api.post('/cart/items', {
        product_id: productId,
        quantity: quantity,
      });
      console.log('Item added to cart', response.data);
      if (response.data.cart) {
        setCartItems(response.data.cart.items);
        setCartTotal(response.data.total);
      }
      return response.data;
    } catch (error) {
      console.error('Failed to add item to cart', error);
      throw error;
    }
  };

  const updateCartItem = async (itemId, quantity) => {
    try {
      // FIX: Changed from POST to PATCH to follow RESTful standards for partial updates
      const response = await api.patch(`/cart/items/${itemId}`, {
        quantity: quantity,
      });
      console.log('Cart item updated', response.data);
      if (response.data.cart) {
        setCartItems(response.data.cart.items);
        setCartTotal(response.data.total);
      }
    } catch (error) {
      console.error('Failed to update cart item', error);
    }
  };

  const removeFromCart = async (itemId) => {
    try {
      await api.delete(`/cart/items/${itemId}`);
      console.log('Cart item removed', itemId);
      
      // FIX: Implemented shallow copying using spread operator to avoid direct state mutation.
      // Previously, 'const updatedItems = cartItems' was copying the reference, causing mutation via splice().
      const updatedItems = [...cartItems];
      const index = updatedItems.findIndex(item => item.id === itemId);
      if (index > -1) {
        updatedItems.splice(index, 1);
      }
      setCartItems(updatedItems);

      let total = 0;
      updatedItems.forEach(item => {
        total += item.price * item.quantity;
      });
      setCartTotal(total);
    } catch (error) {
      console.error('Failed to remove item from cart', error);
    }
  };

  const clearCart = () => {
    setCartItems([]);
    setCartTotal(0);
  };

  return (
    <CartContext.Provider
      value={{
        cartItems,
        cartTotal,
        loading,
        fetchCart,
        addToCart,
        updateCartItem,
        removeFromCart,
        clearCart,
      }}
    >
      {children}
    </CartContext.Provider>
  );
};

export const useCart = () => useContext(CartContext);
