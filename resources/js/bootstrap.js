/**
 * Optimized Bootstrap with lazy loading
 */

// Lazy load axios only when needed
let axiosInstance = null;

const getAxios = async () => {
    if (!axiosInstance) {
        const axios = (await import('axios')).default;
        axiosInstance = axios;
        axiosInstance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        // Add CSRF token if available
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axiosInstance.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    }
    return axiosInstance;
};

// Export for use in other modules
window.getAxios = getAxios;

// For backward compatibility, load axios immediately if needed
// Comment this out if you want full lazy loading
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
