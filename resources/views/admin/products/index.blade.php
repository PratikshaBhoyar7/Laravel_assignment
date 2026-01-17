<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
        nav { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 30px; color: white; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; font-weight: 600; }
        nav button { background: rgba(255,255,255,0.2); color: white; border: 1px solid white; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
        nav button:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; font-weight: 600; }
        .btn:hover { background: #5568d3; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #d68910; }
        .search-box { margin-bottom: 20px; }
        .search-box input { width: 300px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #ddd; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        .actions { display: flex; gap: 10px; }
        .actions a, .actions button { padding: 8px 12px; font-size: 13px; }
        .loading { text-align: center; padding: 20px; color: #666; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a, .pagination span { padding: 8px 12px; margin: 0 3px; }
    </style>
</head>
<body>
    <nav>
        <h2>Admin Panel</h2>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="header">
            <h1>Products</h1>
            <a href="{{ route('admin.products.create') }}" class="btn">+ Add Product</a>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by name or SKU...">
        </div>

        <div id="productsContainer">
            <div class="loading">Loading products...</div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const productsContainer = document.getElementById('productsContainer');

        function loadProducts(search = '') {
            const url = new URL('{{ route('admin.products.index') }}');
            url.searchParams.append('search', search);

            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    renderProducts(data);
                }
            };

            xhr.onerror = function() {
                productsContainer.innerHTML = '<div class="loading">Error loading products</div>';
            };

            xhr.send();
        }

        function renderProducts(data) {
            const products = data.data || [];

            if (products.length === 0) {
                productsContainer.innerHTML = '<div class="loading">No products found</div>';
                return;
            }

            let html = '<table><thead><tr><th>ID</th><th>Name</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody>';

            products.forEach(product => {
                const statusBadge = product.is_active ? '<span style="color: #27ae60;">Active</span>' : '<span style="color: #e74c3c;">Inactive</span>';
                html += `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.name}</td>
                        <td>${product.sku}</td>
                        <td>$${parseFloat(product.price).toFixed(2)}</td>
                        <td>${product.stock}</td>
                        <td>${statusBadge}</td>
                        <td class="actions">
                            <a href="/admin/products/${product.id}/edit" class="btn" style="background: #3498db; padding: 6px 10px; font-size: 12px;">Edit</a>
                            <form method="POST" action="/admin/products/${product.id}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                            <form method="POST" action="/admin/products/${product.id}/toggle" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning" style="padding: 6px 10px; font-size: 12px;">${product.is_active ? 'Deactivate' : 'Activate'}</button>
                            </form>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';

            if (data.links && data.links.length > 0) {
                html += '<div class="pagination">' + data.links.map(link => {
                    if (link.url === null) return `<span>${link.label}</span>`;
                    return `<a href="${link.url}">${link.label}</a>`;
                }).join('') + '</div>';
            }

            productsContainer.innerHTML = html;
        }

        searchInput.addEventListener('input', function(e) {
            loadProducts(e.target.value);
        });

        loadProducts();
    </script>
</body>
</html>
