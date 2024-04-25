import { create } from 'zustand';
import { persist, createJSONStorage, StateStorage } from 'zustand/middleware';
import { AuthStudentState, StudentData } from '../@types/auth/auth-types';
import Cookies from 'js-cookie';

const authStorage: StateStorage = {
  getItem: async (name: string) => Cookies.get(name) ?? null,
  setItem: async (name: string, value: string) =>
    Cookies.set(name, value, { expires: 1 / 24, secure: true }),
  removeItem: async (name: string) => Cookies.remove(name),
};

export const useStudentStore = create<AuthStudentState>()(
  persist(
    (set) => ({
      user: null,
      login: (user: StudentData) => set({ user }),
      logout: () => set({ user: null }),
    }),
    {
      name: 'zustand_student_session',
      storage: createJSONStorage(() => authStorage),
    }
  )
);

export const useStudentAuthApiState = create<{
  isLoading: boolean;
  isError: boolean;
  errorMessage: string;
  setLoading: (isLoading: boolean) => void;
  setError: (isError: boolean, errorMessage: string) => void;
  resetState: () => void;
}>((set) => ({
  isLoading: false,
  isError: false,
  errorMessage: '',
  setLoading: (isLoading: boolean) => set({ isLoading }),
  setError: (isError: boolean, errorMessage: string) => set({ isError, errorMessage }),
  resetState: () => set({ isLoading: false, isError: false, errorMessage: '' }),
}));
