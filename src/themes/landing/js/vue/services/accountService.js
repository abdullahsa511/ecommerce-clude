class AccountService {
    constructor() {
        this.apiUrl = '/api/order-tracking?order_number=';
        this.projectListUrl = '/api/account/project-list';
        this.baseUrl = '/api/';
    }

    async getProjectList(userId) {
        try {
            const url = this.projectListUrl + '/' + userId;
            const response = await fetch(url);
            const apiData = await response.json();
            return { data: apiData };
        } catch (err) {
            console.error('API fetch failed, error=', err);
            return { error: err.message };
        }
    }

    async getOrderTracking(payload = {}) {
        try {
            const orderNumber = payload.orderNumber || '';
            if (orderNumber) {
                // For now return dummy array shaped for the OrderTracking component
                const url = this.apiUrl + orderNumber;
                const response = await fetch(url);
                const apiData = await response.json();
                // console.log('apiData=', apiData);
                return { data: apiData };
            } else {
                return { data: [] };
            }
               
        } catch (err) {
            console.error('API fetch failed, error=', err);
            return { error: err.message };
        }
    }

    async getQuoteAcceptance(payload = {}) {
        try {
            const quoteId = payload.quoteId || '';
            const url = this.baseUrl + 'quote-acceptance?quote_id=' + quoteId;
            const response = await fetch(url);
            const apiData = await response.json();
            console.log('apiData=', apiData);
            return { data: apiData };
        } catch (err) {
            console.error('API fetch failed, error=', err);
            return { error: err.message };
        }
    }

    /**
     * Create a new service request.
     * Supports either a plain object payload (sent as JSON) or a FormData instance (for file uploads).
     */
    async createRequest(payload = {}) {
        try {
            const url = this.baseUrl + 'create-request'; // correct API endpoint

            let fetchOptions = {};
            // If caller passed a FormData instance, send it directly (browser sets boundary)
            if (payload instanceof FormData) {
                fetchOptions = {
                    method: 'POST',
                    body: payload
                };
            } else if (payload && payload.attachments) {
                // If payload contains attachments (File/FileList), build FormData
                const formData = new FormData();
                for (const key in payload) {
                    if (!Object.prototype.hasOwnProperty.call(payload, key)) continue;
                    const value = payload[key];
                    if (value instanceof FileList) {
                        Array.from(value).forEach((file) => formData.append(key, file));
                    } else if (Array.isArray(value)) {
                        value.forEach((v) => formData.append(key + '[]', v));
                    } else {
                        formData.append(key, value);
                    }
                }
                fetchOptions = {
                    method: 'POST',
                    body: formData
                };
            } else {
                // Default: send JSON body
                fetchOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                };
            }

            // console.log('payload=', payload);

            const response = await fetch(url, fetchOptions);
            const apiData = await response.json();
            return { data: apiData };
        } catch (err) {
            console.error('API fetch failed, error=', err);
            return { error: err.message };
        }
    }
    async contactSalesGetInTouch(payload = {}) {
        try {
            const url = this.baseUrl + 'contact-sales-get-in-touch'; // correct API endpoint

            let fetchOptions = {};
            // If caller passed a FormData instance, send it directly (browser sets boundary)
            if (payload instanceof FormData) {
                fetchOptions = {
                    method: 'POST',
                    body: payload
                };
            } else if (payload && payload.attachments) {
                // If payload contains attachments (File/FileList), build FormData
                const formData = new FormData();
                for (const key in payload) {
                    if (!Object.prototype.hasOwnProperty.call(payload, key)) continue;
                    const value = payload[key];
                    if (value instanceof FileList) {
                        Array.from(value).forEach((file) => formData.append(key, file));
                    } else if (Array.isArray(value)) {
                        value.forEach((v) => formData.append(key + '[]', v));
                    } else {
                        formData.append(key, value);
                    }
                }
                fetchOptions = {
                    method: 'POST',
                    body: formData
                };
            } else {
                // Default: send JSON body
                fetchOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                };
            }

            // console.log('payload=', payload);

            const response = await fetch(url, fetchOptions);
            const apiData = await response.json();
            return { data: apiData };
        } catch (err) {
            console.error('API fetch failed, error=', err);
            return { error: err.message };
        }
    }
}
export default new AccountService();
