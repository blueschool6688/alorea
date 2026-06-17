import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import { createContext, useContext, useEffect, useReducer } from 'react';

// Initial state
const initialState = {
    settings: {
        logo_url: '',
        address: '',
        hotline: '',
        email_contact: '',
        link_social_facebook: '',
        link_social_tiktok: '',
        link_social_youtube: '',
        link_social_instagram: '',
        banner_is_image: true,
        banner_image: '',
        banner_video: '',
        title_banner: '',
        introduce_video_manufacture: '',
        introduce_video_design: '',
        sub_title_banner: '',
        banner_video_url : "",
        banner_image_url : "",
        video_tiktok_review: ''
    },
    blogs : [],
    collections : [],
    category : [],
    loading: true,
    error: null,
    isLoaded: false
};

// Action types
const SETTINGS_ACTIONS = {
    SET_LOADING: 'SET_LOADING',
    SET_SETTINGS: 'SET_SETTINGS',
    SET_ERROR: 'SET_ERROR',
    UPDATE_SETTING: 'UPDATE_SETTING',
    CLEAR_ERROR: 'CLEAR_ERROR'
};

// Reducer
const settingsReducer = (state, action) => {
    switch (action.type) {
        case SETTINGS_ACTIONS.SET_LOADING:
            return {
                ...state,
                loading: action.payload
            };

        case SETTINGS_ACTIONS.SET_SETTINGS:
            return {
                ...state,
                settings: { ...state.settings, ...action.payload },
                loading: false,
                error: null,
                isLoaded: true
            };

        case SETTINGS_ACTIONS.SET_ERROR:
            return {
                ...state,
                error: action.payload,
                loading: false
            };

        case SETTINGS_ACTIONS.UPDATE_SETTING:
            return {
                ...state,
                settings: {
                    ...state.settings,
                    [action.payload.key]: action.payload.value
                }
            };

        case SETTINGS_ACTIONS.CLEAR_ERROR:
            return {
                ...state,
                error: null
            };

        default:
            return state;
    }
};

const SettingsContext = createContext();
export const SettingsProvider = ({ children }) => {
    const [state, dispatch] = useReducer(settingsReducer, initialState);
    const fetchSettings = async () => {
        try {
            dispatch({ type: SETTINGS_ACTIONS.SET_LOADING, payload: true });
            const response = await api.get(GeneralPath.GENERAL_DATA_ENDPOINT);
            if (response && response.data) {
                dispatch({
                    type: SETTINGS_ACTIONS.SET_SETTINGS,
                    payload: response.data
                });
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            dispatch({
                type: SETTINGS_ACTIONS.SET_ERROR,
                payload: error.response?.data?.message || error.message || 'Failed to load settings'
            });
        }
    };

    const clearError = () => {
        dispatch({ type: SETTINGS_ACTIONS.CLEAR_ERROR });
    };
    useEffect(() => {
        fetchSettings();
    }, []);
    // Context value
    const value = {
        // State
        settings: state.settings,
        loading: state.loading,
        error: state.error,
        isLoaded: state.isLoaded,
        logo: state.settings.logo_url,
        address: state.settings.address,
        hotline: state.settings.hotline,
        emailContact: state.settings.email_contact,
        bannerIsImage: state.settings.banner_is_image,
        bannerVideo: state.settings.banner_video_url,
        bannerImage: state.settings.banner_image_url,
        titleBanner: state.settings.title_banner,
        subTitleBanner: state.settings.sub_title_banner,

        // Actions
        fetchSettings,
        clearError,

        // State checkers
        hasError: !!state.error,
        isLoading: state.loading
    };

    return (
        <SettingsContext.Provider value={value}>
            {children}
        </SettingsContext.Provider>
    );
};

// Custom hook
export const useSettings = () => {
    const context = useContext(SettingsContext);

    if (context === undefined) {
        throw new Error('useSettings must be used within a SettingsProvider');
    }

    return context;
};

export default SettingsContext;
