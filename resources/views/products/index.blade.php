<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
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
        .cart-link {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .cart-link:hover {
            background-color: #45a049;
        }
        .cart-badge {
            background-color: #ff5722;
            color: white;
            padding: 2px 8px;
            border-radius: 50%;
            font-size: 12px;
            margin-left: 5px;
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
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .product-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .product-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .product-info {
            margin: 10px 0;
            color: #666;
        }
        .product-price {
            font-size: 24px;
            color: #4CAF50;
            font-weight: bold;
            margin: 15px 0;
        }
        .product-stock {
            color: #888;
            font-size: 14px;
        }
        .product-date {
            color: #aaa;
            font-size: 12px;
            margin-top: 10px;
        }
        .btn {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 15px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #0b7dda;
        }
        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .in-cart {
            background-color: #FFC107;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📦 Liste des Produits</h1>
            <a href="{{ route('cart.index') }}" class="cart-link">
                🛒 Panier
                @if(count($cart) > 0)
                    <span class="cart-badge">{{ array_sum($cart) }}</span>
                @endif
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

        <div class="products-grid">
            @forelse($products as $product)
                <div class="product-card">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price">{{ number_format($product->price, 2) }} €</div>
                    <div class="product-info">
                        <div class="product-stock">
                            Stock disponible: <strong>{{ $product->stock }}</strong> unités
                        </div>
                        <div class="product-date">
                            Ajouté le {{ $product->created_at->format('d/m/Y') }}
                        </div>
                    </div>

                    @if(isset($cart[$product->id]))
                        <div class="in-cart">
                            ✓ {{ $cart[$product->id] }} dans le panier
                        </div>
                    @endif

                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn" 
                            @if($product->stock == 0 || (isset($cart[$product->id]) && $cart[$product->id] >= $product->stock)) 
                                disabled 
                            @endif>
                            @if($product->stock == 0)
                                Rupture de stock
                            @elseif(isset($cart[$product->id]) && $cart[$product->id] >= $product->stock)
                                Stock maximum atteint
                            @else
                                Ajouter au panier
                            @endif
                        </button>
                    </form>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">
                    Aucun produit disponible pour le moment.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
