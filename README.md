#Sphinx Search behaviour for CakePHP 2.x
I spent a good few hours googling up on how to get this working so here is the fruits of my labour:


* First install Sphinx
* Get sphinxapi.php from the sphinx distribution and place it in app/vendors.
* Setup an SQL Query for each index (my sphinx.conf is attached)
* Restart searchd (searchd --stop && searchd)
* Run the indexer (indexer --all)
* Test on the command line (search "test")
* Install this behaviour into your CakePHP app
* Example search controller function is below using paginate
* Use a GET method for your form in the view otherwise pagination will lose the search term
* You can search a specific index by adding in 'index' => array('<index name>') into the sphinx array (I am searching the idx_posts index)

```php
public function search() {
            $term = $this->request->query['term'];
            $sphinx = array('matchMode' => 'SPH_MATCH_ALL', 'sortMode' => array('SPH_SORT_EXTENDED' => '@relevance DESC'), 'index' => array('idx_posts'));
            $paginate = array(
                'limit' => 30,
                'contain' => array(
                    'Upvote',
                    'User.id',
                    'User.first_name',
                    'User.last_name',
                    'User.email',
                    'UserDetail.photo',
                    'UserDetail.company',
                    'Category.id',
                    'Category.name',
                    'Type.name'
                ),
                'fields'  => array(
                    'id', 'title', 'body', 'image', 'comment_count', 'upvote_count', 'files', 'explore', 'implement', 'is70','is20','is10', 'free', 'slug', 'created', 'sponsored'
                ),
                'conditions' => array(),
                'order' => array('Post.sponsored' => 'desc', 'Post.created' => 'desc', 'Post.upvote_count' => 'desc'),
                'sphinx' => $sphinx,
                'search' => $term
            );

            $this->paginate = $paginate;

            $this->set('categories', $this->Post->Category->find('all', array('recursive' => -1, 'fields' => array('id','name'))));
            $this->set('posts', $this->paginate());
        }

```





Forked from Nabeel Shahzad:

Updated version for the Sphinx Behavior by Vilen Tambovtsev

The original usage page is here:

http://bakery.cakephp.org/articles/xumix/2009/07/11/sphinx-behavior
