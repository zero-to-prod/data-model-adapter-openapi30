FROM mcr.microsoft.com/devcontainers/base:bullseye

ARG INSTALL_ZSH="true"
ARG UPGRADE_PACKAGES="false"
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID

# Install needed packages and setup non-root user
RUN apt-get update \
    && export DEBIAN_FRONTEND=noninteractive \
    && if [ "${UPGRADE_PACKAGES}" = "true" ]; then apt-get -y upgrade; fi \
    && if [ "${INSTALL_ZSH}" = "true" ]; then \
        apt-get -y install zsh \
        && chsh -s /usr/bin/zsh ${USERNAME}; \
    fi \
    # Clean up
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

# Add alias to bash profile
RUN echo 'alias dock="sh dock"' >> /home/${USERNAME}/.bashrc

# Create and set permissions for /app directory
RUN mkdir -p /app && chown ${USERNAME}:${USERNAME} /app

# Script that will run on container startup
COPY docker-init.sh /usr/local/share/docker-init.sh
RUN chmod +x /usr/local/share/docker-init.sh

ENTRYPOINT [ "/usr/local/share/docker-init.sh" ]
CMD [ "sleep", "infinity" ]