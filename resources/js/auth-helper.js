class AuthHelper {
    constructor() {
        this.baseUrl = window.location.origin;
        this.setupInterceptors();
    }

    setupInterceptors() {
        // Jika menggunakan Axios
        if (typeof axios !== 'undefined') {
            axios.interceptors.response.use(
                (response) => response,
                async (error) => {
                    if (error.response?.status === 401) {
                        const refreshed = await this.attemptTokenRefresh();
                        if (refreshed) {
                            // Retry original request
                            return axios.request(error.config);
                        } else {
                            // Redirect to login
                            window.location.href = '/login';
                        }
                    }
                    return Promise.reject(error);
                }
            );
        }
    }

    async attemptTokenRefresh() {
        try {
            const response = await fetch('/auth/refresh-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    refreshToken: this.getRefreshTokenFromSession()
                })
            });

            if (response.ok) {
                const data = await response.json();
                // Update session atau local storage jika diperlukan
                console.log('Token refreshed successfully');
                return true;
            }
            return false;
        } catch (error) {
            console.error('Token refresh failed:', error);
            return false;
        }
    }

    getRefreshTokenFromSession() {
        // Implementasi untuk mendapatkan refresh token
        // Bisa dari meta tag, hidden input, atau endpoint khusus
        return document.querySelector('meta[name="refresh-token"]')?.content;
    }
}

// Initialize auth helper
const authHelper = new AuthHelper();