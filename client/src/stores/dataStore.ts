// Demo data to test my frontend 
import { defineStore } from 'pinia';

export interface Category {
  id: number;
  name: string;
  parentId: number | null;
}

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  image: string;
  categories: number[];
}

export const useDataStore = defineStore('data', {
  state: (): { categories: Category[]; products: Product[] } => ({
    categories: [
      { id: 1, name: 'Electronics', parentId: null },
      { id: 2, name: 'Phones', parentId: 1 },
      { id: 3, name: 'Laptops', parentId: 1 },
      { id: 4, name: 'Books', parentId: null },
      { id: 5, name: 'Fiction', parentId: 4 },
    ],
    products: [
      {
        id: 1,
        name: 'iPhone',
        description: 'A smartphone',
        price: 999.99,
        image: 'https://i5.walmartimages.com/seo/Simple-Mobile-Apple-iPhone-12-64GB-Black-Prepaid-Smartphone-Locked-to-Simple-Mobile_66b2853b-6cb5-4f20-b73a-b60b39b6de44.6b3bf83a920058a47342318925f1dc2b.jpeg',
        categories: [2],
      },
      {
        id: 2,
        name: 'MacBook',
        description: 'A laptop computer',
        price: 1299.99,
        image: 'https://m.media-amazon.com/images/I/81Q+PMRChcL._AC_UF894,1000_QL80_.jpg',
        categories: [3],
      },
      {
        id: 3,
        name: 'Harry Potter',
        description: 'A book',
        price: 19.99,
        image: 'https://m.media-amazon.com/images/M/MV5BNTU1MzgyMDMtMzBlZS00YzczLThmYWEtMjU3YmFlOWEyMjE1XkEyXkFqcGc@._V1_.jpg',
        categories: [5],
      },
    ],
  }),
  actions: {
    addCategory(category: Omit<Category, 'id'>) {
      const newId = this.categories.length + 1;
      this.categories.push({ id: newId, ...category });
    },
    addProduct(product: Omit<Product, 'id'>) {
      const newId = this.products.length + 1;
      this.products.push({ id: newId, ...product });
    },
  },
});
