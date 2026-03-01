# SJS.Neos.MCP.FeatureSet.Neos

MCP FeatureSet package for **Neos CMS** and **Flow** infrastructure. Provides tools for workspaces, content dimensions, Flow packages, and configuration.

---

## FeatureSets & Tools

### `WorkspaceFeatureSet` — prefix `workspace`

| Tool | Description |
| --- | --- |
| `workspace_list_workspaces` | Lists all workspaces with owner and base workspace info |
| `workspace_list_workspace_changes` | Lists unpublished changes in a workspace |
| `workspace_create_workspace` | Creates a new personal or shared workspace |
| `workspace_delete_workspace` | Deletes a workspace |
| `workspace_publish_workspace` | Publishes all pending changes in a workspace to its base |

### `DimensionFeatureSet` — prefix `dimension`

| Tool | Description |
| --- | --- |
| `dimension_list_dimensions` | Lists all configured content dimensions and their presets |
| `dimension_list_dimension_combinations` | Lists all allowed dimension space point combinations |

### `FlowFeatureSet` — prefix `flow`

| Tool | Description |
| --- | --- |
| `flow_list_packages` | Lists available Flow/Neos packages; filterable by type and key substring |
| `flow_list_configuration_tree` | Returns the merged configuration tree for a given path and context |
