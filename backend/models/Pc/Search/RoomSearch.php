<?php

namespace backend\models\Pc\Search;

use backend\models\Pc\Room;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RouteSearch represents the model behind the search form about `common\models\Route`.
 */
class RoomSearch extends Room
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'roomid'], 'integer'],
            [['roomname','roomadmin'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Room::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query -> orderBy('create_time desc');

        if($this -> roomid){
            $query ->andFilterWhere(['roomid' =>$this -> roomid ]);
        }
        if($this -> roomadmin){
            $query ->andFilterWhere(['like', 'roomadmin', trim($this->roomadmin)]);
        }
        if($this -> roomname){
            $query ->andFilterWhere(['like', 'roomname', trim($this->roomname)]);
        }

        return $dataProvider;
    }
}
