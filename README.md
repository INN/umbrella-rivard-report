## Setup notes

This umbrella repository was created using INN's standalone Vagrant install. To install this:

1. Clone this repository: `git clone git@github.com:INN/umbrella-rivard-report.git`
2. Make sure that your computer has Vagrant installed: `which vagrant` should return something.
3. `cd` to this directory.
4. Run `vagrant up`: this will create a new Virtualbox install with this site's information.
5. Edit your `/etc/hosts` to add `192.168.33.10 vagrant.dev`

## Notice of repository name change

Where this repository was once `INN/rivard-report-umbrella` on Github, it is now `INN/umbrella-rivard-report`. If you had previously cloned this repository, you will need to navigate to this directory on your computer and take the following steps:

1. Run `git remote -v` to list remotes. Make a note of the name that matches `git@github.com:INN/rivard-report-umbrella.git`. It's probably `origin`.
2. Run `git remote set-url origin git@github.com:INN/umbrella-rivard-report.git` where `origin` is the name of the remote you saw in the previous step.
3. Run `git fetch origin`
4. If you have any local working branches that track remote branches, you may need to:
	1. check out the local branch: `git checkout foo`
	2. update the local branch's upstream: `git branch -u origin/foo foo`
