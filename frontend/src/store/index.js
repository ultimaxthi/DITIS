import { createStore } from 'vuex';
import { auth } from './auth';

const store = createStore({
  strict: process.env.NODE_ENV !== 'production',
  modules: {
    auth
  },
});

export default store;