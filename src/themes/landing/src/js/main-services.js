async function contactSalesGetInTouch(payload = {}) {
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