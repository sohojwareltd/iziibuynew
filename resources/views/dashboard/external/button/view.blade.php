<x-dashboard.external>
    <div class="card">
        <div class="card-body">



            <h3>{{ __('words.button_payment') }}</h3>

            <ul class="list-group">
                <li class="list-group-item">
                    Domain : {{ $paymentApi->domain }}
                </li>
                <li class="list-group-item">
                    Success Url : {{ $paymentApi->success_redirect_url }}
                </li>
                <li class="list-group-item">
                    Failed Url : {{ $paymentApi->failed_redirect_url }}
                </li>
                <li class="list-group-item">
                    Cancel Callback Url : {{ $paymentApi->cancel_callback_url ?? 'Not configured' }}
                </li>
                <li class="list-group-item bg-secondary text-light">
                    Source Key : {{ $paymentApi->key }}
                </li>
            </ul>
            <h3 class="d-flex align-items-center justify-content-between">
                <span>Orders</span>
                <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $orders->total() }} total</span>
            </h3>

            <form action="" method="GET" class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-lg-4">
                            <label class="form-label text-uppercase small fw-semibold">Search</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                class="form-control" placeholder="Jane Doe">
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <label class="form-label text-uppercase small fw-semibold">Paid between</label>
                            <div class="input-group">
                                <input type="date" name="paid_from" value="{{ $filters['paid_from'] ?? '' }}"
                                    class="form-control">
                                <span class="input-group-text">—</span>
                                <input type="date" name="paid_to" value="{{ $filters['paid_to'] ?? '' }}"
                                    class="form-control">
                            </div>
                            <small class="text-muted">Uses the paid_at timestamp.</small>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <label class="form-label text-uppercase small fw-semibold">Paid Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="PENDING"
                                    {{ ($filters['status'] ?? '') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                <option value="COMPLETED"
                                    {{ ($filters['status'] ?? '') === 'COMPLETED' ? 'selected' : '' }}>Completed
                                </option>

                            </select>
                            <small class="text-muted">Uses the paid_at timestamp.</small>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('external.buttonPayment.view', $paymentApi) }}"
                                class="btn btn-light border">Reset</a>
                            <button type="submit" class="btn btn-primary">Apply filters</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Payment ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid at</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->id }}</td>
                                <td>{{ $order->orderId ?? '—' }}</td>
                                <td><span class="font-monospace">{{ $order->payment_id ?? '—' }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customer_name ?? '—' }}</div>
                                    @if ($order->description)
                                        <small class="text-muted">{{ $order->description }}</small>
                                    @endif
                                </td>
                                <td>{{ $order->customer_email ?? '—' }}</td>


                              
                                <td>
                                    <div class="fw-semibold">{{ number_format((float) $order->amount, 2) }}
                                        {{ $order->currency }}</div>
                                </td>
                                <td>
                                    @php
                                        $status = strtoupper($order->status ?? 'PENDING');
                                        $statusClass =
                                            [
                                                'COMPLETED' => 'bg-success-subtle text-success',
                                                'FAILED' => 'bg-danger-subtle text-danger',
                                                'CANCELED' => 'bg-secondary-subtle text-secondary',
                                                'PENDING' => 'bg-warning-subtle text-warning',
                                            ][$status] ?? 'bg-light text-muted';
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-2">{{ $status }}</span>
                                </td>
                                <td>
                                    @php
                                        $paidAt = $order->paid_at
                                            ? \Illuminate\Support\Carbon::parse($order->paid_at)
                                            : null;
                                    @endphp
                                    {{ $paidAt?->format('Y-m-d H:i') ?? '—' }}
                                </td>
                                <td>{{ $order->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if (strtoupper($order->status ?? 'PENDING') === 'PENDING')
                                            <form action="{{ route('external.buttonPayment.cancel', ['paymentApi' => $paymentApi, 'order' => $order->id]) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="voyager-x"></i> Cancel
                                                </button>
                                            </form>
                                         
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">
                                    <div class="fs-5 fw-semibold">No orders found</div>
                                    <p class="mb-0">Try adjusting your filters or create a new payment request.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $orders->appends(request()->query())->links() }}
            </div>
            <h3>
                How to use
            </h3>
            <h5>Javascript :</h5>
            <pre>
                <code class="text-dark">
                    &lt;div id="iziipay"&gt; &lt;/div&gt;
                    &lt;script src="{{ asset('payment/iziipay.js') }}"&gt;&lt;/script&gt;
                    &lt;script&gt;
                        Iziipay.init('#iziipay', {
                            apiKey: "{{ $paymentMethodAccess->key }}",
                            buttonText: 'Pay now',
                            source_key: "{{ $paymentApi->key }}",
                            amount: "300",
                            taxValue: "10%",
                            taxTotal: "27.27",
                            orderId: "300",
                            description: "T-Shirt Purchase",
                            currency: "NOK",
                        });
                    &lt;/script&gt;
                </code>
            </pre>
            <hr>
            <div class=" api-doc-container">

                <h5>Api Endpoint :</h5>
                <pre><code>POST {{ route('iziipay.createPayment', $paymentMethodAccess->key) }}</code></pre>

                <h5>Description</h5>
                <p>This endpoint creates a new external order and generates a payment link using the specified payment
                    API.</p>

                <h5>Request Parameters</h5>

                <h6>Body Parameters</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Required</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>source_key</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Unique API key for the payment source.</td>
                        </tr>
                        <tr>
                            <td>name</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Customer's full name.</td>
                        </tr>
                        <tr>
                            <td>email</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Customer's email address.</td>
                        </tr>
                        <tr>
                            <td>phone</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Customer's phone number.</td>
                        </tr>
                        <tr>
                            <td>country</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Customer's country of residence.</td>
                        </tr>
                        <tr>
                            <td>address</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Customer's street address.</td>
                        </tr>
                        <tr>
                            <td>post_code</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Customer's postal/ZIP code.</td>
                        </tr>
                        <tr>
                            <td>amount</td>
                            <td>float</td>
                            <td>Yes</td>
                            <td>Payment amount to be processed.</td>
                        </tr>
                        <tr>
                            <td>currency</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Currency for the payment (e.g., NOK).</td>
                        </tr>
                        <tr>
                            <td>taxValue</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Tax Value (e.g., 10%).</td>
                        </tr>
                        <tr>
                            <td>taxTotal</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Tax Value (e.g., 27.4).</td>
                        </tr>
                        <tr>
                            <td>description</td>
                            <td>string</td>
                            <td>Yes</td>
                            <td>Tax Value (e.g., T-shirt purchase).</td>
                        </tr>

                        <tr>
                            <td>orderId</td>
                            <td>integer</td>
                            <td>Yes</td>
                            <td>Tax Value (e.g., 1234).</td>
                        </tr>
                    </tbody>
                </table>

                <h2>Response</h2>
                <h6>Success Response</h6>
                <pre><code class="code-box">
            {
                "url": "https://payment-gateway.com/payment-link"
            }
                </code></pre>

                <h6>Error Responses</h6>
                <pre><code class="code-box">
            400 Bad Request
            {
                "error": "Invalid source key provided."
            }
            
            404 Not Found
            {
                "error": "Payment method or API not found."
            }
            
            500 Internal Server Error
            {
                "error": "An unexpected error occurred. Please try again later."
            }
                </code></pre>

                <h5>Example Usage</h5>
                <h6>cURL Command</h6>
                <pre><code class="code-box">
            curl -X POST {{ route('iziipay.createPayment', $paymentMethodAccess->key) }} \
            -H "Content-Type: application/json" \
            -d '{
                "source_key": "{{ $paymentApi->key }}",
                "name": "John Doe",
                "email": "john.doe@example.com",
                "phone": "1234567890",
                "country": "Norway",
                "address": "123 Main Street",
                "post_code": "12345",
                "amount": 100.00,
                "currency": "NOK"
            }'
                </code></pre>

            </div>

            <hr>
            <div class="api-doc-container">
                <h5>Cancel Payment API Endpoint :</h5>
                <pre><code>POST {{ route('iziipay.cancel.surfboard.payment', $paymentMethodAccess->key) }}</code></pre>

                <h5>Description</h5>
                <p>This endpoint cancels an existing external order payment. This method is only supported for Surfboard
                    payment method.</p>

                <h5>Request Parameters</h5>

                <h6>Body Parameters</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Required</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>order_id</td>
                            <td>integer</td>
                            <td>Yes</td>
                            <td>The ID of the external order to cancel.</td>
                        </tr>
                    </tbody>
                </table>

                <h2>Response</h2>
                <h6>Success Response</h6>
                <pre><code class="code-box">
            {
                "status": true,
                "code": 200,
                "data": {
                    "orderStatus": "CANCELED",
                    "message": "Order canceled successfully"
                }
            }
                </code></pre>

                <h6>Error Responses</h6>
                <pre><code class="code-box">
            400 Bad Request
            {
                "message": "No order found. Please send a valid order_id"
            }
            
            400 Bad Request
            {
                "message": "This method is not supported for this payment method"
            }
            
            500 Internal Server Error
            {
                "status": false,
                "code": 500,
                "data": "Error message from payment gateway"
            }
                </code></pre>

                <h5>Example Usage</h5>
                <h6>cURL Command</h6>
                <pre><code class="code-box">
            curl -X POST {{ route('iziipay.cancel.surfboard.payment', $paymentMethodAccess->key) }} \
            -H "Content-Type: application/json" \
            -d '{
                "order_id": 123
            }'
                </code></pre>

            </div>

            <hr>
            <div class="api-doc-container">
                <h5>Cancel Order Callback (Admin to User) :</h5>
                <p><strong>Important:</strong> Administrators will call <strong>YOUR</strong> callback URL (the one you provide in "Cancel Callback URL" field) to notify you about order cancellations.</p>
                
                <h5>How It Works</h5>
                <ol>
                    <li>You provide your callback URL in the "Cancel Callback URL" field when creating/editing your Payment API</li>
                    <li>When an admin needs to cancel an order, they will send a GET request to <strong>YOUR</strong> callback URL</li>
                    <li>Your server receives the request and processes the cancellation on your end</li>
                </ol>

                <h5>Admin's Request Format</h5>
                <p>Administrators will call your callback URL with the following format:</p>
                <pre><code>GET {YOUR_CALLBACK_URL}?order_id={order_id}</code></pre>
                
                <h6>Example:</h6>
                <p>If you set your callback URL as: <code>https://yourdomain.com/api/cancel-order</code></p>
                <p>Admin will call: <code>https://yourdomain.com/api/cancel-order?order_id=123</code></p>

                <h5>Request Parameters (What Admin Sends)</h5>

                <h5>Request Parameters</h5>

                <h6>Query Parameters</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Required</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>order_id</td>
                            <td>integer</td>
                            <td>Yes</td>
                            <td>The ID of the external order to cancel.</td>
                        </tr>
                    </tbody>
                </table>

                <h5>What Your Server Should Do</h5>
                <p>When your callback URL receives the request from admin, you should:</p>
                <ol>
                    <li>Extract the <code>order_id</code> parameter from the request</li>
                    <li>Process the cancellation on your end (update your database, notify customers, etc.)</li>
                    <li>Optionally, you can call our cancel API endpoint (see below) to cancel the order in our system</li>
                    <li>Return a response (any HTTP status code is acceptable)</li>
                </ol>

                <h6>Example PHP Handler</h6>
                <pre><code class="code-box">
            // Your callback endpoint handler (e.g., Laravel route)
            Route::get('/api/cancel-order', function (Request $request) {
                $orderId = $request->query('order_id');
                
                // Process cancellation on your end
                // Update your database, send notifications, etc.
                
                // Optionally, call our cancel API to cancel in our system
                // Http::get('{{ route('buttonPayment.cancelCallback') }}?order_id=' . $orderId);
                
                return response()->json([
                    'status' => 'received',
                    'order_id' => $orderId
                ]);
            });
                </code></pre>

                <h6>Example Node.js Handler</h6>
                <pre><code class="code-box">
            // Express.js example
            app.get('/api/cancel-order', (req, res) => {
                const orderId = req.query.order_id;
                
                // Process cancellation on your end
                // Update database, send notifications, etc.
                
                res.json({
                    status: 'received',
                    order_id: orderId
                });
            });
                </code></pre>



            </div>


        </div>
    </div>
</x-dashboard.external>
