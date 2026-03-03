<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Products Inventory ({{ $products->total() }})
        </h2>
    </x-slot>

    {{-- Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search + Buttons --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" name="q" value="{{ $search ?? '' }}" 
                       class="form-control me-2" placeholder="Search name/SKU...">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('products.create') }}" class="btn btn-success me-2">➕ Add Product</a>
            {{-- CSV Form --}}
            <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="d-inline">
                @csrf
                <div class="input-group input-group-sm">
                    <input type="file" name="csv" accept=".csv" class="form-control">
                    <button type="submit" class="btn btn-info">📥 Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- COMPACT TABLE --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered">
            <thead class="table-dark sticky-top">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 200px;">Name</th>
                    <th style="width: 100px;">SKU</th>
                    <th style="width: 70px;">Stock</th>
                    <th style="width: 90px;">Price</th>
                    <th style="width: 100%; min-width: 300px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="align-middle">
                    {{-- Compact Data --}}
                    <td class="text-center fw-bold">{{ $product->id }}</td>
                    <td class="text-truncate" style="max-width: 200px;" title="{{ $product->name }}">
                        {{ Str::limit($product->name, 30) }}
                    </td>
                    <td><code class="small">{{ $product->sku }}</code></td>
                    <td class="text-center">
                        <span class="badge p-2 {{ $product->stock > 5 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="text-end fw-bold">${{ number_format($product->price, 2) }}</td>
                    
                    {{-- SINGLE LINE ACTIONS --}}
                    <td class="p-1 align-middle" style="white-space: nowrap !important;">
                        <div class="d-flex align-items-center gap-1">
                            {{-- Edit --}}
                            <a href="{{ route('products.edit', $product) }}" 
                               class="btn btn-sm btn-warning text-white px-2 py-0" style="height: 28px; min-width: 32px;" title="Edit">
                                <i class="fas fa-edit me-0"></i>
                            </a>
                            
                            {{-- Delete --}}
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline px-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger px-2 py-0" 
                                        style="height: 28px; min-width: 32px;" onclick="return confirm('Delete?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            
                            {{-- Sell --}}
                            <form method="POST" action="{{ route('products.sale', $product) }}" class="d-inline px-1">
                                @csrf
                                <div class="input-group input-group-sm" style="width: 85px; height: 28px;">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                           class="form-control form-control-sm border-end-0 p-1" style="font-size: 12px; height: 28px;">
                                    <button type="submit" class="btn btn-primary btn-sm p-1 px-2" 
                                            style="border-left: 0; height: 28px;" title="Sell">
                                        <i class="fas fa-dollar-sign"></i>
                                    </button>
                                </div>
                            </form>
                            
                            {{-- Audit --}}
                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0 me-2" 
                                    style="height: 28px;" onclick="toggleAudit({{ $product->id }})" title="Audit Log">
                                📋 {{ $product->audits()->count() }}
                            </button>
                        </div>
                    </td>
                </tr>
                
                {{-- Audit Details Row --}}
                <tr id="audit-row-{{ $product->id }}" style="display: none;">
                    <td colspan="6" class="bg-light p-3">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-2">📊 Audit History ({{ $product->audits()->count() }})</h6>
                                <div class="row">
                                    @forelse($product->audits()->latest()->limit(3)->get() as $audit)
                                    <div class="col-md-4 mb-2">
                                        <small class="text-muted">
                                            {{ $audit->created_at->format('M d H:i') }}<br>
                                            <span class="badge bg-info">{{ $audit->before_stock }}</span>
                                            → <span class="badge bg-warning">{{ $audit->after_stock }}</span><br>
                                            <span class="text-primary">{{ ucfirst($audit->action) }}</span>
                                        </small>
                                    </div>
                                    @empty
                                    <div class="col-12"><small class="text-muted">No audits yet</small></div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i><br>
                        No products found. <a href="{{ route('products.create') }}" class="text-primary">Create first</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $products->appends(request()->query())->links() }}
    </div>

    {{-- JavaScript --}}
    @push('scripts')
    <script>
    function toggleAudit(productId) {
        const row = document.getElementById('audit-row-' + productId);
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    }
    </script>
    @endpush
</x-app-layout>
