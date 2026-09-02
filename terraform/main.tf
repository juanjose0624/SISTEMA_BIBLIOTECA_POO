terraform {
  required_providers {
    docker = {
      source  = "kreuzwerker/docker"
      version = "~> 3.0"
    }
  }
}

provider "docker" {}

# Red para conectar los dos contenedores
resource "docker_network" "biblioteca_net" {
  name = "biblioteca-net"
}

# Volumen para persistir los datos de la base de datos
resource "docker_volume" "db_data" {
  name = "biblioteca-db-data"
}

# Contenedor de la base de datos
resource "docker_image" "db" {
  name = var.db_image
}

resource "docker_container" "db" {
  name  = "biblioteca-db-tf"
  image = docker_image.db.image_id

  networks_advanced {
    name = docker_network.biblioteca_net.name
  }

  env = [
    "MYSQL_ROOT_PASSWORD=${var.db_root_password}",
    "MYSQL_DATABASE=${var.db_name}"
  ]

  volumes {
    volume_name    = docker_volume.db_data.name
    container_path = "/var/lib/mysql"
  }
}

# Contenedor de la aplicación
resource "docker_image" "app" {
  name = var.app_image
}

resource "docker_container" "app" {
  name  = "biblioteca-app-tf"
  image = docker_image.app.image_id

  networks_advanced {
    name = docker_network.biblioteca_net.name
  }

  env = [
    "DB_HOST=biblioteca-db-tf",
    "DB_USER=root",
    "DB_PASS=${var.db_root_password}",
    "DB_NAME=${var.db_name}"
  ]

  ports {
    internal = 80
    external = var.app_port
  }

  depends_on = [docker_container.db]
}
