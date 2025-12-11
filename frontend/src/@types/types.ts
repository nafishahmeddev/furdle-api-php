export interface ApiResponse<T = undefined> {
  code: string
  result: T
  message: string
}

export interface FaceRecord {
  face_id: number
  [key: string]: unknown
}

export interface FaceSearchResponse {
  result: FaceRecord[]
  [key: string]: unknown
}

export interface FaceOperationResponse {
  success: boolean
  message?: string
  [key: string]: unknown
}

export type ThirdPartyLookupApiResponse = ApiResponse<{
  admission_session_id: number
  payload: Record<string, string>,
  query: Record<string, string>,
  student: {
    form_no: number,
    class_name: string,
    student_name: string,
    image: string,
  }
  token: string
  url: string
}>