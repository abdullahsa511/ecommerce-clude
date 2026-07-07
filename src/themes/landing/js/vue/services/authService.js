import Service from './service.js';
import { Auth, User } from '../models/User.js';
import { Pinboard } from '../models/Pinboard.js';
class AuthService extends Service {
    constructor() {
        super();
        this.localAuthKey = 'userAuthDetails';
    }
    async isLoggedIn() {
        try {
            const userAuthDetails = await this.getUserAuthentication();
            if (userAuthDetails && !!userAuthDetails?.user?.user_id && userAuthDetails.access_token) {
                return userAuthDetails;
            }
            return false;
        } catch (error) {
            throw new Error(error.message);
        }
    }

    async getCustomer() {
        try {
            const userAuthDetails = await this.getUserAuthentication();
            return userAuthDetails?.customer || {};
        } catch (error) {
            console.error("Invalid customer JSON in localStorage:", error);
            return {};
        }
    }
    async setCustomer(customer) {
        let userAuthDetails = await this.getUserAuthentication();
        if (!userAuthDetails) {
            userAuthDetails = {customer: {}};
        }
        if(customer && typeof customer === 'object') {
            userAuthDetails.customer = customer;
            await this.setUserAuthentication(userAuthDetails);
        }
    }

    // check user login
    async checkUserLogin() {
        this.isLoggedIn();
    }

    async checkExistingCustomer(email) {
        const url = '/api/check-existing-customer';
        try {
            const response = await this.post(url, { email });
            if (!response.ok) {
                throw new Error('API fetch failed, error=', response.statusText);
            }
            return await response.json();
        }catch (error) {
            throw new Error(error.message);
        }
    }

    async sendEmailVerification(email) {
        const url = '/api/send-email-verification';
        try {
            const subject = 'OTP Verification with Krost';
            const response = await this.post(url, { email, subject });
            if (!response.ok) {
                throw new Error('API fetch failed, error=', response.statusText);
            }
            return await response.json();
        }catch (error) {
            throw new Error(error.message);
        }
    }

    async registerCustomer(customer) {
        const url = '/api/register-customer';
        try {
            const response = await this.post(url, { customer });
            if (!response.ok) {
                throw new Error('API fetch failed, error=', response.statusText);
            }
            const responseData = await response.json();
            await this.setUserAuthentication(new Auth({ customer: responseData.customer }));
            return responseData;
        }catch (error) {
            throw new Error(error.message);
        }
    }

    async verifyEmail(email, otp) {
        const url = '/api/verify-email';
        try {
            const response = await this.post(url, { email, otp });
            if (!response.ok) {
                throw new Error('API fetch failed, error=', response.statusText);
            }
            const { user, customer } = await response.json();
            await this.setUserAuthentication(new Auth({ user, customer }));
        }catch (error) {
            throw new Error(error.message);
        }
    }
    async verifyEmailAthenticateAndCreatePinboard(email, otp, pinboard) {
        try {
            const url = '/api/verify-email-authenticate-and-create-pinboard';
            const response = await this.post(url, { email, otp, pinboard });

            if (!response.ok) {
                const error = await response.json();
                console.log('error=', error);
                throw new Error(error.message || 'Network response was not ok');
            }
            const apiData = await response.json();
            const auth = apiData.auth; 
            auth.user = apiData.user;
            auth.customer = apiData.customer;
            this.setUserAuthentication(new Auth(auth));
            new CustomEvent('user:isLoggedIn', { detail: true });
            return apiData;
        }catch (err) {
            throw new Error(err.message);
        }
    }
    async authenticateFromAccessTokenCookie() {
        if (!this.hasAuthCookieSignal()) {
            return null;
        }
        const url = '/api/auth';
        try {
            const response = await this.post(url, {});
            if (!response.ok) {
                return null;
            }

            const responseData = await response.json();
            if (!responseData?.auth || !responseData?.user) {
                return null;
            }

            if(responseData.pinboard) {
                const pinboard = new Pinboard(responseData.pinboard);
                localStorage.setItem('pinboard', JSON.stringify(pinboard));
            }

            const authPayload = new Auth({
                ...responseData.auth,
                user: responseData.user,
                customer: responseData.customer || null,
            });
            await this.setUserAuthentication(authPayload);

            return authPayload;
        } catch (error) {
            return null;
        }
    }

    hasAuthCookieSignal() {
        if (typeof window !== 'undefined' && typeof window.__AUTH_PRESENT__ === 'boolean') {
            return window.__AUTH_PRESENT__ === true;
        }

        const cookie = typeof document !== 'undefined' ? document.cookie || '' : '';
        if (!cookie) return false;

        // Backward-compatible fallback when server-side auth flag is unavailable.
        return /(?:^|;\s*)(auth_present|access_token|admin_token_type)=/.test(cookie);
    }
    async setUserAuthentication(auth) {
        try {
            if (!auth || typeof auth !== 'object') {
                throw new Error('Invalid login response payload');
            }

            if(auth.user && auth.user.user_id) {
                // Update header dropdown after successful auth:
                // if Account + Logout are missing, inject them.
                try {
                    if (typeof document !== 'undefined') {
                        const menu = document.querySelector('ul[data-v-header-navigation-menus]');
                        if (menu) {
                            const hasAccount = !!menu.querySelector('#account-button');
                            const hasLogout = !!menu.querySelector('#logout-button');

                            if (!hasAccount || !hasLogout) {
                                const replacementUl = `
                                    <ul class="dropdown-menu dropdown-menu-end show" data-v-header-navigation-menus="" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 27.7778px);">
                                        <li data-v-header-navigation-menu-item="">
                                            <a class="dropdown-item" href="/account/virtual-pinboards" id="account-button">Account</a>
                                        </li>
                                        <li data-v-header-navigation-menu-item="">
                                            <a class="dropdown-item" href="/logout" id="logout-button">Logout</a>
                                        </li>
                                    </ul>
                                `.trim();

                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = replacementUl;
                                const newUl = wrapper.firstElementChild;
                                if (newUl) menu.replaceWith(newUl);
                            }
                        }
                    }
                } catch (e) {
                    // If DOM isn't ready or selectors don't match, do not break auth.
                    // eslint-disable-next-line no-console
                    console.warn('authService header menu update skipped:', e);
                }
            }

            // Encode key + value via base64, then persist encoded auth payload.
            const encodedAuthKey = btoa(this.localAuthKey);
            const encodedAuthValue = btoa(JSON.stringify(auth));
            localStorage.setItem(encodedAuthKey, encodedAuthValue);

            // Decode once with atob for sanity and keep backward compatibility
            // with existing methods (isLoggedIn/getCustomer) that read plain keys.
            const decodedPayload = JSON.parse(atob(encodedAuthValue));

            return decodedPayload;
        } catch (error) {
            throw new Error(error.message || 'Failed to set logging in user');
        }
    }

    async getUserAuthentication() {
        try {
            // Prefer encoded storage: btoa(key) -> btoa(JSON value)
            const encodedAuthKey = btoa(this.localAuthKey);
            const encodedAuthValue = localStorage.getItem(encodedAuthKey);
            if (encodedAuthValue && encodedAuthValue !== "undefined") {
                const decoded = JSON.parse(atob(encodedAuthValue));
                return new Auth(decoded);
            }
            return null;
        } catch (error) {
            throw new Error(error.message || 'Failed to get user authentication');
        }
    }

    async logout() {
        try {
            const localAuthKey = 'userAuthDetails';
            const encodedAuthKey = btoa(localAuthKey);
            localStorage.removeItem(encodedAuthKey);
            localStorage.removeItem('pinboard');
            localStorage.removeItem('customer');
            localStorage.removeItem('pinboard_processed');
    
            // Clear client-accessible auth cookies before server logout.
            const expireCookie = (name) => {
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax`;
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=None; Secure`;
            };
    
            ['access_token', 'admin_access_token', 'admin_token_type', 'admin_refresh_token', 'auth_present'].forEach(expireCookie);
        } catch (error) {
            console.log(error);
        }
    }

    _e(text, key) {
        let result = "";
        for (let i = 0; i < text.length; i++) {
          result += String.fromCharCode(text.charCodeAt(i) ^ key.charCodeAt(i % key.length));
        }
        return btoa(result); 
    }
      
    _d(encoded, key) {
        let decoded = atob(encoded); 
        let result = "";
        for (let i = 0; i < decoded.length; i++) {
            result += String.fromCharCode(decoded.charCodeAt(i) ^ key.charCodeAt(i % key.length));
        }
        return result;
    }
}
export default new AuthService();