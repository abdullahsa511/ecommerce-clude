import Customer from "./Customer.js";
export class User {
    constructor(data = {}) {
        this.user_id = data?.user_id ?? null;
        this.email = data?.email ?? null;
        this.first_name = data?.first_name ?? null;
        this.last_name = data?.last_name ?? null;
        this.phone = data?.phone ?? null;
    }
}
export class Auth {
    constructor(data = {}) {
        this.token_type = data?.token_type ?? 'Bearer';
        this.access_token = data?.access_token ?? null;
        this.refresh_token = data?.refresh_token ?? null;
        this.expires_in = data?.expires_in ?? null;
        this.session = !!data?.session;
        this.user = data?.user ?new User(data.user) : null;
        this.customer = data?.customer ?new Customer(data.customer) : null;
    }

    isAuthenticated() {
        return !!(this.session || this.access_token);
    }
}

