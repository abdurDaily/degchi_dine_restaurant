@php
    $orderItems = $order->normalizedItems();
    $isEditable = $order->status === 'pending';
@endphp
<div class="table-responsive">
    <table class="table table-hover order-items-table mb-0 align-middle">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Price</th>
                <th class="text-center" style="width:140px;">Qty</th>
                <th class="text-end pe-4">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orderItems as $index => $item)
                @php
                    $title = $item['title'] ?? $item['name'] ?? 'Item';
                    $price = (float) ($item['original_price'] ?? $item['price'] ?? 0);
                    $qty = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
                    $image = $item['image'] ?? null;
                    $note = $item['note'] ?? null;
                    $offerPercent = (int) ($item['offer_percent'] ?? 0);
                    $offerDiscount = (float) ($item['offer_discount'] ?? 0);
                    $discountedUnit = $offerPercent > 0 && $qty > 0
                        ? max(0, $price - ($offerDiscount / $qty))
                        : $price;
                    $lineSubtotal = ($price * $qty) - $offerDiscount;
                @endphp
                <tr data-item-index="{{ $index }}">
                    <td>
                        <div class="d-flex align-items-center">
                            @if(!empty($image))
                                <img src="{{ str_starts_with($image, 'http') ? $image : asset($image) }}" alt="{{ $title }}" class="order-item-thumb me-2">
                            @else
                                <div class="order-item-thumb order-item-thumb-placeholder me-2">
                                    <i class="ri-restaurant-line"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold text-dark">{{ $title }}</div>
                                @if(!empty($note))
                                    <small class="text-muted">{{ $note }}</small>
                                @endif
                                @if($offerPercent > 0)
                                    <span class="badge bg-danger-subtle text-danger ms-1">{{ $offerPercent }}% OFF</span>
                                @endif
                                @if(!empty($item['added_by_admin']))
                                    <span class="badge bg-info-subtle text-info ms-1">Added by admin</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($offerPercent > 0)
                            <span class="text-decoration-line-through text-muted d-block" style="font-size:.78rem;">৳{{ number_format($price, 2) }}</span>
                            <span class="fw-semibold text-danger">৳{{ number_format($discountedUnit, 2) }}</span>
                        @else
                            ৳{{ number_format($price, 2) }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isEditable)
                            <div class="order-qty-stepper" data-order-id="{{ $order->id }}" data-item-index="{{ $index }}">
                                <button type="button" class="btn-qty-step order-qty-minus" aria-label="Decrease quantity">
                                    <i class="ri-subtract-line"></i>
                                </button>
                                <span class="order-qty-value">{{ $qty }}</span>
                                <button type="button" class="btn-qty-step order-qty-plus" aria-label="Increase quantity">
                                    <i class="ri-add-line"></i>
                                </button>
                                <button type="button" class="btn-qty-remove order-item-remove" title="Remove item">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        @else
                            <span class="fw-bold">{{ $qty }}</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold pe-4">৳{{ number_format($lineSubtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No items stored for this order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
