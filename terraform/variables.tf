variable "app_image" {
  description = "Nombre de la imagen de la app"
  type        = string
  default     = "biblioteca-app:v1"
}

variable "db_image" {
  description = "Imagen de la base de datos"
  type        = string
  default     = "mariadb:10.4"
}

variable "db_root_password" {
  description = "Password root de la base de datos"
  type        = string
  sensitive   = true
}

variable "db_name" {
  description = "Nombre de la base de datos"
  type        = string
  default     = "biblioteca"
}

variable "app_port" {
  description = "Puerto externo para acceder a la app"
  type        = number
  default     = 8091
}