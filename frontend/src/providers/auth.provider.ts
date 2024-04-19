import { create } from 'zustand';
import { persist, createJSONStorage, StateStorage } from 'zustand/middleware';
import { AuthState, UserData } from '../@types/auth/auth-types';
import Cookies from 'js-cookie';

const authStorage: StateStorage = {
  getItem: async (name) => Cookies.get(name) ?? null,
  setItem: async (name, value) => Cookies.set(name, value, { expires: 1 / 24, secure: true }),
  removeItem: async (name) => Cookies.remove(name),
};

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      login: (user: UserData) => set({ user }),
      logout: () => set({ user: null }),
    }),
    {
      name: 'zustand-auth',
      storage: createJSONStorage(() => authStorage),
    },
  ),
);
