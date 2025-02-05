export default interface ProductPayload {
    name: string;
    description: string;
    price: number;
    image?: File | string; 
    categories: string[]; 
  }