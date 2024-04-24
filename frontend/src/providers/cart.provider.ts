import { create } from 'zustand';

interface BookCartStore {
  totalCart: number;
  maxCart: number;
  addToCart: () => void;
  removeFromCart: () => void;
}

export const useBookCartStore = create<BookCartStore>((set) => ({
  totalCart: 0,
  maxCart: 2,
  addToCart: () =>
    set((state) => ({
      totalCart: state.totalCart < state.maxCart ? state.totalCart + 1 : state.totalCart,
    })),
  removeFromCart: () => set((state) => ({ totalCart: state.totalCart - 1 })),
}));
