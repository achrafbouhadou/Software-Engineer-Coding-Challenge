
export interface CategoryPayload {
  name: string;
  parent_id?: string;
}

export interface Category {
  id: string;
  name: string;
  parent_id: string | null;
  children: Category[];
  created_at: Date;
  updated_at: Date;
}