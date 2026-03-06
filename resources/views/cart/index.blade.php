<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 28px;
        }
        .products-link {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .products-link:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .cart-empty {
            background-color: white;
            padding: 40px;
            text-align: center;
            border-radius: 8px;
            color: #888;
        }
        .cart-table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background-color: #2196F3;
            color: white;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            font-weight: bold;
        }
        tbody tr {
            border-bottom: 1px solid #eee;
        }
        tbody tr:hover {
            background-color: #f9f9f9;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-quantity {
            background-color: #2196F3;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        .btn-quantity:hover {
            background-color: #0b7dda;
        }
        .btn-quantity:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .quantity-display {
            font-weight: bold;
            min-width: 30px;
            text-align: center;
        }
        .btn-remove {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-remove:hover {
            background-color: #da190b;
        }
        .total-section {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: right;
        }
        .total-label {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .total-amount {
            font-size: 36px;
            font-weight: bold;
            color: #4CAF50;
        }
        .price {
            color: #4CAF50;
            font-weight: bold;
        }
        .stock-warning {
            color: #ff9800;
            font-size: 12px;
        }
        .stock-ok {
            color: #4CAF50;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🛒 Mon Panier</h1>
            <a href="{{ route('products.index') }}" class="products-link">
                📦 Continuer mes achats
            </a>
        </header>

        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ✗ {{ session('error') }}
            </div>
        @endif

        @if(count($products) == 0)
            <div class="cart-empty">
                <h2>Votre panier est vide</h2>
                <p style="margin-top: 20px;">Ajoutez des produits depuis la page produits!</p>
            </div>
        @else
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité au panier</th>
                            <th>Stock restant</th>
                            <th>Sous-total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td><strong>{{ $product['name'] }}</strong></td>
                                <td class="price">{{ number_format($product['price'], 2) }} €</td>
                                <td>
                                    <div class="quantity-controls">
                                        <form action="{{ route('cart.decrement', $product['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-quantity">-</button>
                                        </form>
                                        
                                        <span class="quantity-display">{{ $product['quantity'] }}</span>
                                        
                                        <form action="{{ route('cart.increment', $product['id']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-quantity" 
                                                @if($product['quantity'] >= $product['stock']) disabled @endif>
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $product['stock'] }}</strong> unités
                                    @if($product['stock'] - $product['quantity'] <= 0)
                                        <div class="stock-warning">⚠ Stock épuisé</div>
                                    @elseif($product['stock'] - $product['quantity'] <= 5)
                                        <div class="stock-warning">⚠ Stock faible</div>
                                    @else
                                        <div class="stock-ok">✓ En stock</div>
                                    @endif
                                </td>
                                <td class="price">{{ number_format($product['subtotal'], 2) }} €</td>
                                <td>
                                    <form action="{{ route('cart.remove', $product['id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-remove">🗑 Retirer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="total-label">Total du panier:</div>
                <div class="total-amount">{{ number_format($total, 2) }} €</div>
            </div>
        @endif
    </div>
</body>
</html>
