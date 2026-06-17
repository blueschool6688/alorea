import { createSlice } from '@reduxjs/toolkit';

const initialState = {
    logo: null,
    setting: {},
    error: ""
}


const generalSlice = createSlice({
    name: 'general',
    initialState,
    reducers: {
        setGeneral: (state, action) => {
            state.logo = action.payload.logoURL;
            state.setting = action.payload.setting;
            state.error = '';
        },
    }
})

export const { setGeneral } = generalSlice.actions

export default generalSlice.reducer
