#!/usr/bin/env python
'''
This module implements Kanboard Json-RPC API in Python.

Example:
    kanboard = Kanboard("kanboard-token")
    
    projects = kanboard.getAllProjects()
    
    for p in projects:
        proj = kanboard.getProjectById(p['id'])
		
        pprint(proj)
		
		categories = kanboard.getAllCategories(p['id'])
        for c in categories:
                category = kanboard.getCategory(c['id'])
				
                try:
                    pprint(category)
                except:
                    print("Cannot print category %s" % category['id'])
                    continue
		
        tasks = kanboard.getAllTasks(p['id'], 1)
        for t in tasks:
            task = kanboard.getTask(t['id'])
			
            try:
                pprint(task)
            except:
                print("Cannot print task %s" % task['id'])
                continue
             
            subtasks = kanboard.getAllSubtasks(task['id'])
            for st in subtasks:
                subtask = kanboard.getSubtask(st['id'])
                     
                try:
                    pprint(subtask)
                except:
                    print("Cannot print subtask %s" % subtask['id'])
                    continue
             
            comments = kanboard.getAllComments(task['id'])
            for c in comments:
                comment = kanboard.getComment(c['id'])
                 
                try:
                    pprint(comment)
                except:
                    print("Cannot print comment %s" % comment['id'])
                    continue

Created on 18.09.2014

@author: dzudek
'''

import requests
import json
from datetime import datetime


def getfromid(iid, ilist, arg='name'):
    for u in ilist:
        if u['id'] == iid:
            return u[arg]


getdate = lambda timestamp: getfromtimestamp("%x", timestamp)

getdatetime = lambda timestamp: getfromtimestamp("%x %X", timestamp)

getfromtimestamp = lambda format, timestamp: datetime.fromtimestamp(int(timestamp)).strftime(format) if timestamp is not None else None

class Kanboard():
    url = "http://localhost/kanboard/jsonrpc.php"
    headers = {'content-type': 'application/json'}
    
    username = "jsonrpc"
    token = None
    
    _id = 0
    
    def __init__(self, token, url = None):
        if url is not None:
            self.url = url
            
        self.token = token
    
    def _getId(self):
        self._id += 1
        return self._id
        
    def createProject(self, name):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createProject",
                 "id" : kid,
                 "params": {
                        "name": name
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getProjectById(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getProjectById",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getProjectByName(self, name):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getProjectByName",
                 "id" : kid,
                 "params": {
                        "name": name
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
        
    def getAllProjects(self):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllProjects",
                 "id" : kid
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
        
    def updateProject(self, project_id, name, is_active = None, token = None, is_public = None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateProject",
                 "id" : kid,
                 "params": {
                        "id": project_id,
                        "name": name
                 }
        }
        
        #optional parameters  
        if is_active is not None:
            params['params']["is_active"] = is_active
        if token is not None:
            params['params']["token"] = token
        if is_public is not None:
            params['params']["is_public"] = is_public
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def enableProject(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "enableProject",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def disableProject(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "disableProject",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeProject(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeProject",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def enableProjectPublicAccess(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "enableProjectPublicAccess",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def disableProjectPublicAccess(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "disableProjectPublicAccess",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getAllowedUsers(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllowedUsers",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def revokeUser(self, project_id, user_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "revokeUser",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "user_id": user_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def allowUser(self, project_id, user_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "allowUser",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "user_id": user_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getBoard(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getBoard",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getColumns(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getColumns",
                 "id" : kid,
                 "params": {
                        "project_id": project_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getColumn(self, column_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getColumn",
                 "id" : kid,
                 "params": {
                        "column_id": column_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def moveColumnUp(self, project_id, column_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "moveColumnUp",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "column_id": column_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def moveColumnDown(self, project_id, column_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "moveColumnDown",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "column_id": column_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def updateColumn(self, column_id, title, task_limit=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateColumn",
                 "id" : kid,
                 "params": {
                        "column_id": column_id,
                        "title": title,                        
                 }
        }
        
        #optional parameter  
        if task_limit is not None:
            params['params']["task_limit"] = task_limit
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def addColumn(self, project_id, title, task_limit=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "addColumn",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "title": title,                        
                 }
        }
        
        #optional parameter  
        if task_limit is not None:
            params['params']["task_limit"] = task_limit
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeColumn(self, column_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeColumn",
                 "id" : kid,
                 "params": {
                        "column_id": column_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def createTask(self, project_id, title, color_id=None, column_id=None, description=None, owner_id=None, creator_id=None, score=None, date_due=None, category_id=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createTask",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "title": title,                        
                 }
        }
        
        #optional parameters
        if color_id is not None:
            params['params']["color_id"] = color_id
        if column_id is not None:
            params['params']["column_id"] = column_id
        if description is not None:
            params['params']["description"] = description
        if owner_id is not None:
            params['params']["owner_id"] = owner_id
        if creator_id is not None:
            params['params']["creator_id"] = creator_id
        if score is not None:
            params['params']["score"] = score
        if date_due is not None:
            params['params']["date_due"] = date_due
        if category_id is not None:
            params['params']["category_id"] = category_id
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getTask(self, task_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getTask",
                 "id" : kid,
                 "params": {
                        "task_id": task_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getAllTasks(self, project_id, status):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllTasks",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "status": status
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def updateTask(self, task_id, project_id=None, title=None, color_id=None, column_id=None, description=None, owner_id=None, creator_id=None, score=None, date_due=None, category_id=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createTask",
                 "id" : kid,
                 "params": {
                        "id": task_id,                     
                 }
        }
        
        #optional parameters
        if title is not None:
            params['params']["title"] = title
        if project_id is not None:
            params['params']["project_id"] = project_id
        if color_id is not None:
            params['params']["color_id"] = color_id
        if column_id is not None:
            params['params']["column_id"] = column_id
        if description is not None:
            params['params']["description"] = description
        if owner_id is not None:
            params['params']["owner_id"] = owner_id
        if creator_id is not None:
            params['params']["creator_id"] = creator_id
        if score is not None:
            params['params']["score"] = score
        if date_due is not None:
            params['params']["date_due"] = date_due
        if category_id is not None:
            params['params']["category_id"] = category_id
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def openTask(self, task_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "openTask",
                 "id" : kid,
                 "params": {
                        "task_id": task_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def closeTask(self, task_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "closeTask",
                 "id" : kid,
                 "params": {
                        "task_id": task_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeTask(self, task_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeTask",
                 "id" : kid,
                 "params": {
                        "task_id": task_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def moveTaskPosition(self, project_id, task_id, column_id, position):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeTask",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "task_id": task_id,
                        "column_id": column_id,
                        "position": position
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def createUser(self, username, password, name=None, email=None, is_admin=None, default_project_id=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createUser",
                 "id" : kid,
                 "params": {
                        "username": username, 
                        "password": password                    
                 }
        }
        
        #optional parameters
        if name is not None:
            params['params']["name"] = name
        if email is not None:
            params['params']["email"] = email
        if is_admin is not None:
            params['params']["is_admin"] = is_admin
        if default_project_id is not None:
            params['params']["default_project_id"] = default_project_id
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getUser(self, user_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getUser",
                 "id" : kid,
                 "params": {
                        "user_id": user_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getAllUsers(self):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllUsers",
                 "id" : kid
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def updateUser(self, user_id, username=None, name=None, email=None, is_admin=None, default_project_id=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateUser",
                 "id" : kid,
                 "params": {
                        "id": user_id               
                 }
        }
        
        #optional parameters
        if username is not None:
            params['params']["username"] = username
        if name is not None:
            params['params']["name"] = name
        if email is not None:
            params['params']["email"] = email
        if is_admin is not None:
            params['params']["is_admin"] = is_admin
        if default_project_id is not None:
            params['params']["default_project_id"] = default_project_id
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeUser(self, user_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeUser",
                 "id" : kid,
                 "params": {
                        "user_id": user_id
                 }
        }
        
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def createCategory(self, project_id, name):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createCategory",
                 "id" : kid,
                 "params": {
                        "project_id": project_id,
                        "name": name               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getCategory(self, category_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getCategory",
                 "id" : kid,
                 "params": {
                        "category_id": category_id
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getAllCategories(self, project_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllCategories",
                 "id" : kid,
                 "params": {
                        "project_id": project_id    
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def updateCategory(self, category_id, name):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateCategory",
                 "id" : kid,
                 "params": {
                        "id": category_id,
                        "name": name               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeCategory(self, category_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateCategory",
                 "id" : kid,
                 "params": {
                        "id": category_id          
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def createComment(self, task_id, user_id, content):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createComment",
                 "id" : kid,
                 "params": {
                        "task_id": task_id,
                        "user_id": user_id,
                        "content": content               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getComment(self, comment_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getComment",
                 "id" : kid,
                 "params": {
                        "comment_id": comment_id           
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getAllComments(self, task_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllComments",
                 "id" : kid,
                 "params": {
                        "task_id": task_id           
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def updateComment(self, comment_id, content):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateComment",
                 "id" : kid,
                 "params": {
                        "id": comment_id,
                        "content": content               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeComment(self, comment_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeComment",
                 "id" : kid,
                 "params": {
                        "id": comment_id               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def createSubtask(self, task_id, title, assignee_id=None, time_estimated=None, time_spent=None, status=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "createSubtask",
                 "id" : kid,
                 "params": {
                        "task_id": task_id,
                        "title": title        
                 }
        }
        
        #optional parameters
        if assignee_id is not None:
            params['params']["assignee_id"] = assignee_id
        if time_estimated is not None:
            params['params']["time_estimated"] = time_estimated
        if time_spent is not None:
            params['params']["time_spent"] = time_spent
        if status is not None:
            params['params']["status"] = status
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getSubtask(self, subtask_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getSubtask",
                 "id" : kid,
                 "params": {
                        "subtask_id": subtask_id               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def getAllSubtasks(self, task_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "getAllSubtasks",
                 "id" : kid,
                 "params": {
                        "task_id": task_id               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def updateSubtask(self, subtask_id, task_id, title=None, assignee_id=None, time_estimated=None, time_spent=None, status=None):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "updateSubtask",
                 "id" : kid,
                 "params": {
                        "id": subtask_id,
                        "task_id": task_id
                 }
        }
        
        #optional parameters        
        if title is not None:
            params['params']["title"] = title
        if assignee_id is not None:
            params['params']["assignee_id"] = assignee_id
        if time_estimated is not None:
            params['params']["time_estimated"] = time_estimated
        if time_spent is not None:
            params['params']["time_spent"] = time_spent
        if status is not None:
            params['params']["status"] = status
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']
    
    def removeSubtask(self, subtask_id):
        kid = self._getId()   
        params = {
                 "jsonrpc": "2.0",
                 "method": "removeSubtask",
                 "id" : kid,
                 "params": {
                        "subtask_id": subtask_id               
                 }
        }
            
        response = requests.post(self.url, data=json.dumps(params), headers=self.headers, auth=(self.username, self.token))
        
        assert response.ok
        assert response.json()['id'] == kid
        
        return response.json()['result']

