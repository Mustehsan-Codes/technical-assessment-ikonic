import React, { useState, useEffect } from 'react';
import api from '../services/api';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';

const Products = () => {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('');
  const [search, setSearch] = useState('');
  const { user } = useAuth();
  const { addToCart } = useCart();

  useEffect(() => {
    fetchCategories();
    fetchProducts();
  }, []);

  const fetchCategories = async () => {
    const response = await api.get('/categories');
    setCategories(response.data.data);
  };

  const fetchProducts = async () => {
    let url = '/products';
    const params = {};
    if (selectedCategory) params.category_id = selectedCategory;
    if (search) params.search = search;

    const response = await api.get(url, { params });
    setProducts(response.data.data);
  };

  useEffect(() => {
    fetchProducts();
  }, [selectedCategory]);

  const handleSearch = (e) => {
    e.preventDefault();
    fetchProducts();
  };

  const handleAddToCart = async (productId) => {
    if (!user) {
      alert('Please login to add items to cart');
      return;
    }
    addToCart(productId, 1);
    alert('Added to cart!');
  };

  return (
    <div style={styles.container}>
      <h1 style={styles.heading}>Products</h1>

      <div style={styles.filters}>
        <div style={styles.categoryFilter}>
          <select
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
            style={styles.select}
          >
            <option value="">All Categories</option>
            {categories.map((cat) => (
              <option key={cat.id} value={cat.id}>
                {cat.name}
              </option>
            ))}
          </select>
        </div>
        <form onSubmit={handleSearch} style={styles.searchForm}>
          <input
            type="text"
            placeholder="Search products..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            style={styles.searchInput}
          />
          <button type="submit" style={styles.searchBtn}>
            Search
          </button>
        </form>
      </div>

      <div style={styles.grid}>
        {products.map((product) => (
          <div key={product.id} style={styles.card}>
            <div style={styles.cardBody}>
              <span style={styles.category}>{product.category}</span>
              <h3 style={styles.productName}>{product.name}</h3>
              <p style={styles.description}>{product.description}</p>
              <div style={styles.priceRow}>
                <span style={styles.price}>${product.price}</span>
                <span style={styles.stock}>
                  {product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
                </span>
              </div>
              <button
                onClick={() => handleAddToCart(product.id)}
                style={styles.addBtn}
                disabled={product.stock === 0}
              >
                Add to Cart
              </button>
            </div>
          </div>
        ))}
      </div>

      {products.length === 0 && (
        <p style={styles.empty}>No products found.</p>
      )}
    </div>
  );
};

const styles = {
  container: { maxWidth: '1200px', margin: '0 auto', padding: '24px' },
  heading: { color: '#2c3e50', marginBottom: '20px' },
  filters: {
    display: 'flex',
    gap: '16px',
    marginBottom: '24px',
    flexWrap: 'wrap',
  },
  categoryFilter: {},
  select: {
    padding: '10px',
    borderRadius: '4px',
    border: '1px solid #ddd',
    fontSize: '14px',
  },
  searchForm: { display: 'flex', gap: '8px' },
  searchInput: {
    padding: '10px',
    borderRadius: '4px',
    border: '1px solid #ddd',
    fontSize: '14px',
    minWidth: '200px',
  },
  searchBtn: {
    padding: '10px 20px',
    backgroundColor: '#3498db',
    color: '#fff',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
  },
  grid: {
    display: 'grid',
    gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))',
    gap: '20px',
  },
  card: {
    background: '#fff',
    borderRadius: '8px',
    boxShadow: '0 2px 8px rgba(0,0,0,0.1)',
    overflow: 'hidden',
  },
  cardBody: { padding: '16px' },
  category: {
    fontSize: '12px',
    color: '#7f8c8d',
    textTransform: 'uppercase',
    letterSpacing: '1px',
  },
  productName: { margin: '8px 0', color: '#2c3e50', fontSize: '18px' },
  description: { color: '#666', fontSize: '14px', marginBottom: '12px' },
  priceRow: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: '12px',
  },
  price: { fontSize: '20px', fontWeight: 'bold', color: '#27ae60' },
  stock: { fontSize: '13px', color: '#95a5a6' },
  addBtn: {
    width: '100%',
    padding: '10px',
    backgroundColor: '#2c3e50',
    color: '#fff',
    border: 'none',
    borderRadius: '4px',
    cursor: 'pointer',
    fontSize: '14px',
  },
  empty: { textAlign: 'center', color: '#999', marginTop: '40px' },
};

export default Products;
