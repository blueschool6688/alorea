import { configureStore } from '@reduxjs/toolkit';
import { persistStore } from 'redux-persist';
import storage from 'redux-persist/lib/storage';
import generalSlice from '@/slices/generalSlice';
// const commoinfig = {
//     key: 'auth',
//     storage,
//     version: 1,
// };

// const userCongig = {
//     ...commoinfig,
//     whitelist: ['currentUser', 'isAuthenticated', 'isAdmin'],
// };

const generalConfig = {
    key: 'general',
    storage,
    version: 1,
    whitelist: ['logo', 'setting'],
};

export const store = configureStore({
    reducer: {
        general: generalSlice,
        // auth: persistReducer(userCongig, authSlice),
        // category: categorySlice
    },
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware({
            serializableCheck: false,
        }),
});

export const persiststore = persistStore(store);
