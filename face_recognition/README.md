
# Installation Guide

1.  **Install pyenv:**

```bash
sudo apt-get update

sudo apt install -y make build-essential libssl-dev zlib1g-dev \
  libbz2-dev libreadline-dev libsqlite3-dev wget curl llvm \
  libncursesw5-dev xz-utils tk-dev libxml2-dev libxmlsec1-dev \
  libffi-dev liblzma-dev git

curl https://pyenv.run | bash

```


2.  **Configure pyenv:**

```bash
echo 'export PATH="$HOME/.pyenv/bin:$PATH"' >> ~/.bashrc
echo 'eval "$(pyenv init --path)"' >> ~/.bashrc
echo 'eval "$(pyenv init -)"' >> ~/.bashrc
echo 'eval "$(pyenv virtualenv-init -)"' >> ~/.bashrc
source ~/.bashrc
```

3.  **Install Python version:**

```bash
pyenv install 3.11.12
pyenv local 3.11.12
```

4.  **Create a virtual environment:**

```bash
python -m venv env
source env/bin/activate
```

5.  **Install dependencies:**

```bash
pip install -r requirements.txt
```

6.  **Run the application:**

```bash
uvicorn main:app --reload --port 8001
```