// Theme Management Module
const ThemeManager = (() => {
  const toggleTheme = () => {
    const body = document.body;
    body.classList.toggle('dark-theme');

    const button = document.getElementById('theme-toggle');
    button.classList.toggle('dark-theme');

    // Persist theme selection in localStorage
    const theme = body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
  };

  const applySavedTheme = () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      document.body.classList.add('dark-theme');
      document.getElementById('theme-toggle').classList.add('dark-theme');
    }
  };

  return { toggleTheme, applySavedTheme };
})();

// Product Management Module
const ProductManager = (() => {
  const productList = document.getElementById('products-container');

  const fetchProducts = async () => {
    productList.innerHTML = '<p>Loading products...</p>';
    try {
      const response = await fetch('includes/get_products.php');
      if (!response.ok) throw new Error(`Error ${response.status}: Failed to fetch products`);

      const products = await response.json();
      renderProducts(products);
    } catch (error) {
      productList.innerHTML = `<p>Error loading products: ${error.message}. Please try again later.</p>`;
      console.error('Error fetching products:', error);
    }
  };

  const renderProducts = (products) => {
    productList.innerHTML = '';
    if (products.length === 0) {
      productList.innerHTML = '<p>No products available.</p>';
      return;
    }

    products.forEach(product => {
      const productCard = document.createElement('div');
      productCard.classList.add('product-item');
      productCard.innerHTML = `
        <img src="${product.image || 'images/sample-product.jpg'}" alt="${product.name}" style="max-width: 200px;">
        <h3>${product.name}</h3>
        <p>${product.description}</p>
        <p class="price">$${product.price.toFixed(2)}</p>
        <button class="edit-btn" onclick="ModalManager.openEditModal(${product.id}, '${product.name}', '${product.description}', ${product.price})">Edit</button>
        <button class="delete-btn" onclick="ModalManager.openDeleteModal(${product.id})">Delete</button>
      `;
      productList.appendChild(productCard);
    });
  };

  const addProduct = async (formData) => {
    try {
      const response = await fetch('includes/add_product.php', {
        method: 'POST',
        body: formData,
      });
      if (!response.ok) throw new Error(`Error ${response.status}: Failed to add product`);

      const data = await response.json();
      if (data.success) {
        alert('Product added successfully!');
        fetchProducts();
      } else {
        alert('Error adding product: ' + data.message);
      }
    } catch (error) {
      console.error('Error adding product:', error);
      alert('An error occurred while adding the product. Please try again.');
    }
  };

  const updateProduct = async (data) => {
    try {
      const response = await fetch('includes/update_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });
      if (!response.ok) throw new Error(`Error ${response.status}: Failed to update product`);

      const result = await response.json();
      if (result.success) {
        alert('Product updated successfully!');
        fetchProducts();
        ModalManager.closeModal('edit-modal');
      } else {
        alert('Error updating product: ' + result.message);
      }
    } catch (error) {
      console.error('Error updating product:', error);
      alert('An error occurred while updating the product. Please try again.');
    }
  };

  const deleteProduct = async (id) => {
    try {
      const response = await fetch('includes/delete_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id }),
      });
      if (!response.ok) throw new Error(`Error ${response.status}: Failed to delete product`);

      const result = await response.json();
      if (result.success) {
        alert('Product deleted successfully!');
        fetchProducts();
        ModalManager.closeModal('delete-modal');
      } else {
        alert('Error deleting product: ' + result.message);
      }
    } catch (error) {
      console.error('Error deleting product:', error);
      alert('An error occurred while deleting the product. Please try again.');
    }
  };

  return { fetchProducts, addProduct, updateProduct, deleteProduct };
})();

// Modal Management Module
const ModalManager = (() => {
  const openEditModal = (id, name, description, price) => {
    const editModal = document.getElementById('edit-modal');
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-product-id').value = id;
    editModal.classList.remove('hidden');
  };

  const openDeleteModal = (id) => {
    const deleteModal = document.getElementById('delete-modal');
    document.getElementById('confirm-delete-btn').onclick = () => ProductManager.deleteProduct(id);
    deleteModal.classList.remove('hidden');
  };

  const closeModal = (modalId) => {
    document.getElementById(modalId).classList.add('hidden');
  };

  return { openEditModal, openDeleteModal, closeModal };
})();

// Initialization
window.onload = () => {
  ThemeManager.applySavedTheme();
  ProductManager.fetchProducts();

  document.getElementById('theme-toggle').addEventListener('click', ThemeManager.toggleTheme);

  document.getElementById('product-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const formData = new FormData(event.target);
    ProductManager.addProduct(formData);
    event.target.reset();
  });
};
