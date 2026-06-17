const General = {
    HOMEPAGE: '/',
    REGISTER: "/register",
    PRODUCTS: '/products',
    PRODUCT_DETAIL: '/products/:code',
    COLLECTIONS: '/collections',
    BLOGS: '/blogs',
    ABOUT: '/about',
    PROFILE: '/profile',
    CHECKOUT: '/checkout',
    ORDER_PAYMENT: '/order/:orderId',
    BLOG_DETAIL: '/blog/:slug',
    SEARCH_ORDER: '/search-order',
    NOTFOUND: '/404'
}

const EndpointAPI = {
    REGISTER_ENDPOINT: 'register',
    LOGIN_ENDPOINT: 'login',
    UPDATE_PROFILE_ENDPOINT: 'update-profile',
    GET_ORDERS_DATA_ENDPOINT: 'get-orders',
    GET_ORDER_ENDPOINT: 'get-order',
    GET_ORDER_GUEST_ENDPOINT: 'guest/get-order',
    CHANGE_PASSWORD_ENDPOINT: 'change-password',
    USER_ENDPOINT: 'user',
    LOGOUT_ENDPOINT: 'logout',
    PROVINCES_ENDPOINT: 'get-provinces',
    WARDS_ENDPOINT: 'get-wards',
    STORE_ORDER_ENDPOINT: 'store-order',
    GENERAL_DATA_ENDPOINT: 'get-general',
    GENERAL_DATA_HOMEPAGE_ENDPOINT: 'get-data-homepage',
    GENERAL_DATA_COLLECTIONS_ENDPOINT: 'get-collections',
    GENERAL_DATA_PRODUCT_DETAIL_ENDPOINT: 'detail-product',
    GENERAL_DATA_PRODUCTS_ENDPOINT: 'get-products',
    GENERAL_DATA_BLOGS_DETAIL_ENDPOINT: 'get-blogs/detail'
}

const TokenKey = {
    USER_KEY: 'userData',
    AUTH_TOKEN_KEY: 'authToken'
}


export default {
    ...General,
    ...EndpointAPI,
    ...TokenKey
}
