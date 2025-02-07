export interface ProductPayload {
  name: string;
  description: string;
  price: number;
  image?: File | string;
  categories: string[]; 
}
export interface Product {
  id: string;
  name: string;
  description: string;
  price: number;
  image?: string;
  categories: string[];
  created_at: Date;
  updated_at: Date;
}