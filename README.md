# Furry Music DB

## Local Development Instructions

* Install Docker for your host operating system (or Docker Desktop)

### First Time Launch

```bash
cp auth_config.dev.json auth_config.json

make build
make up
```

### To Shutdown

```bash
make down
```

### To access container BASH

```bash
make bash
```

Or as root:

```bash
make bash-root
```
