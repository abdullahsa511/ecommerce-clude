export default {
  name: 'OrderTracking',

  data() {
    return {};
  },

  computed: {
    trackingOrders() {
      return this.$store.getters.trackingOrders || { tracking: [] };
    },
    order() {
      // Normalize possible shapes from the store:
      // - an array of orders => use first element
      // - a single order object => return it
      // - an object that wraps tracking data (e.g. { tracking: [...] }) => try to find order fields
      const storeValue = this.$store.getters.trackingOrders || {};

      // If the store value is an array, return first element if present
      if (Array.isArray(storeValue)) {
        return storeValue.length ? storeValue[0] : null;
      }

      // If it has a 'tracking' array and also top-level order fields, prefer top-level order fields
      if (storeValue && typeof storeValue === 'object') {
        // Common direct order fields
        if (storeValue.order_id || storeValue.customer_order_id || storeValue.invoice_no) {
          return storeValue;
        }

        // If there's an 'order' nested object, return that
        if (storeValue.order && typeof storeValue.order === 'object') {
          return storeValue.order;
        }

        // Fallback: if it's an object with numeric keys or single-entry, try to return it
        if (Object.keys(storeValue).length > 0) {
          return storeValue;
        }
      }

      return null;
    }
  },

  created() {
    // Only dispatch if store has no order tracking data to avoid duplicate requests
    try {
      const ot = this.$store.state.orderTracking;
      const isEmptyArray = Array.isArray(ot) && ot.length === 0;
      const isEmptyObject = ot && typeof ot === 'object' && !Array.isArray(ot) && Object.keys(ot).length === 0;
      if (isEmptyArray || isEmptyObject) {
        this.$store.dispatch('getOrderTracking');
      }
    } catch (err) {
      // If anything goes wrong, avoid breaking the component lifecycle
      console.error('OrderTracking created() guard failed', err);
    }
  },

  watch: {
    order(newVal) {
      console.log('order= watch', newVal);
    }
  },

  methods: {
    formatDate(date) {
      if (!date) return '-';
      const d = new Date(date);
      return d.toLocaleDateString('en-GB'); // DD/MM/YYYY
    },
    formatCurrency(amount) {
      const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      });
      return formatter.format(amount);
    },
    calculateSubtotal(items) {
      if (!Array.isArray(items)) return '0.00';
      return items.reduce((sum, item) => sum + (item.total_price || 0), 0).toFixed(2);
    },
    calculateGST(items) {
      return (parseFloat(this.calculateSubtotal(items)) * 0.1).toFixed(2); // 10% GST
    },
    calculateTotal(items) {
      return (parseFloat(this.calculateSubtotal(items)) + parseFloat(this.calculateGST(items))).toFixed(2);
    },
    showSubtotal(items) {
      return this.formatCurrency(this.calculateSubtotal(items));
    },
    showGST(items) {
      return this.formatCurrency(this.calculateGST(items));
    },
    showTotal(items) {
      return this.formatCurrency(this.calculateTotal(items));
    },
    viewDetails(orderId) {
      if (!orderId) return;
      window.location.href = `/account/orders/${orderId}`;
    }
  },

  template: /* html */ `
  <div class="customer-order-route child-route-container">
    <div class="customer-order-overview">

      <!-- Order Status Steps -->
      <div class="p-card p-component page-component customer-order-status">
        <div class="p-card-header">
          <h3 class="header-name">Order Status</h3>
        </div>
        <div class="p-card-body">
          <div class="p-card-content">
            <div v-if="!trackingOrders || !trackingOrders.tracking || trackingOrders.tracking.length === 0">
              <p class="text-center text-danger">No tracking data available</p>
            </div>
            <div v-else class="p-card-content">
              <div class="p-steps p-component step-form-header" style="width: 100%;">
                <ul role="tablist">
                  <li v-for="step in trackingOrders.tracking"
                      :key="step.order_status_id"
                      class="p-steps-item"
                      :class="{'p-highlight': step.completed, 'p-steps-current': step.completed}">
                    <span class="p-menuitem-link" role="presentation">
                      <span class="p-steps-number">
                        <i :class="step.completed ? 'fa-solid fa-check' : ''"></i>
                      </span>
                      <span class="p-steps-title text-capitalize">{{ step.name || '-' }}</span>
                    </span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Header -->
      <div v-if="order" class="p-card p-component page-component customer-order-overview-details">
        <div class="p-card-header">
          <h3 class="order-summary-title">
            <a
              v-if="order.order_id"
              :href="'/account/orders/' + order.order_id"
              target="_blank"
              class="order-summary-link"
            >
              Order: {{ order.order_id }} - Invoice #{{ order.invoice_no || '-' }}
            </a>
            <span v-else class="order-summary-link">
              Order: - Invoice #{{ order.invoice_no || '-' }}
            </span>
          </h3>
        </div>

        <div class="p-card-body">
          <div class="p-card-content">

            <!-- Left Side: Customer & Shipping Info -->
            <div class="customer-order-details-left">
              <div class="customer-order-dates">
                <div class="p-field">
                  <label>Order Placed</label>
                  <span>{{ formatDate(order.created_at) }}</span>
                </div>
                <div class="p-field">
                  <label>Reference Number</label>
                  <span>{{ order.reference_number || '-' }}</span>
                </div>
                <div class="p-field">
                  <label>Payment Method</label>
                  <span>{{ order.payment_method || '-' }}</span>
                </div>
                <div class="p-field">
                  <label>Shipping Method</label>
                  <span>{{ order.shipping_method || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Center Side: Delivery Address -->
            <div class="customer-order-address">
                <p class="order-tracking-del-title">Delivery Address</p>
                <span class="address-info p-field">
                  {{ order.shipping_first_name || '' }} {{ order.shipping_last_name || '' }}
                  <br>
                  {{ order.shipping_company || '' }}<br>
                  {{ order.shipping_address_1 || '' }} {{ order.shipping_address_2 || '' }}<br>
                 {{ order.shipping_city || '' }} - {{ order.shipping_post_code || '' }} {{ order.shipping_country || '' }}
                </span>
            </div>

            <!-- Right Side: Order Totals -->
            <div class="customer-order-details-right">
              <div class="customer-order-totals">
                <div class="customer-order-subtotals">
                  <div class="p-field">
                    <label>Subtotal</label>
                    <span>{{ showSubtotal(order.items) }}</span>
                  </div>
                  <div class="p-field">
                    <label>GST</label>
                    <span>{{ showGST(order.items) }}</span>
                  </div>
                </div>
                <div class="customer-order-grand-total">
                  <div class="p-field">
                    <label>Total (inc. GST)</label>
                    <span>{{ showTotal(order.items) }}</span>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
  `
};
