import axios, {type  AxiosInstance } from 'axios';
import type { 
  ThirdPartyLookupApiResponse, 
  FaceSearchResponse, 
  FaceOperationResponse 
} from '../@types/types';

class ApiService {
  private baseApi: AxiosInstance;
  private faceApi: AxiosInstance | null = null;

  constructor() {
    this.baseApi = axios.create({
      baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8080',
      // Don't set Content-Type by default to avoid triggering preflight requests
    });
  }

  // Initialize face API with dynamic base URL and token
  private initFaceApi(baseUrl: string, token: string): AxiosInstance {
    if (!this.faceApi) {
      this.faceApi = axios.create({
        baseURL: baseUrl,
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });
    } else {
      this.faceApi.defaults.baseURL = baseUrl;
      this.faceApi.defaults.headers['Authorization'] = `Bearer ${token}`;
    }
    return this.faceApi;
  }

  // Third-party lookup
  async thirdPartyLookup(formNo: string, session: string): Promise<ThirdPartyLookupApiResponse> {
    const response = await this.baseApi.post('/api/third-party', {
      form_no: formNo,
      session,
    }, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    return response.data;
  }

  // Search for existing faces
  async searchFaces(baseUrl: string, token: string, query: Record<string, unknown>): Promise<FaceSearchResponse> {
    const faceApi = this.initFaceApi(baseUrl, token);
    const response = await faceApi.post('/faces/search', { query }, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    return response.data;
  }

  // Delete a face
  async deleteFace(baseUrl: string, token: string, faceId: number): Promise<FaceOperationResponse> {
    const faceApi = this.initFaceApi(baseUrl, token);
    const response = await faceApi.delete(`/face/${faceId}`);
    return response.data;
  }

  // Register a new face
  async registerFace(
    baseUrl: string,
    token: string,
    imageFile: File,
    payload: Record<string, unknown>,
    query: Record<string, unknown>
  ): Promise<FaceOperationResponse> {
    const faceApi = this.initFaceApi(baseUrl, token);
    
    const formData = new FormData();
    formData.append('image', imageFile);
    formData.append('payload', JSON.stringify(payload));
    formData.append('query', JSON.stringify(query));

    const response = await faceApi.post('/face/register', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }
}

// Create a singleton instance
export const apiService = new ApiService();

// Keep the existing ApiUtils for backward compatibility
export class ApiUtils {
  static getApiUrl(path: string): string {
    return (import.meta.env.VITE_API_URL || 'http://localhost:8080') + path;
  }
}