import React from 'react';
import {
  TouchableOpacity, ImageBackground, StyleSheet, View,
} from 'react-native';
import { getScreenWidth } from 'utils/size';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import Text from '../Text';
import Card from '../Card';
import Rating from '../Rating';
import axios from 'axios';

const styles = StyleSheet.create({
  container: {
    borderTopLeftRadius: scale(8),
    borderTopRightRadius: scale(8),
    overflow: 'hidden',
    flex: 1,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  info: {
    padding: scale(10),
    justifyContent: 'space-between',
    flex: 1,
  },
  bg: {
    flex: 1,
  },
  between: {
    justifyContent: 'space-between',
  },
  rating: {
    marginVertical: scale(4),
  },
  discount: {
    textDecorationLine: 'line-through',
    marginLeft: scale(4),
  },
});



const  CheckImageOnUrl=({url})=>{
  const [istrue,setIstrue] =React.useState(false);
  React.useEffect(()=>{
    fetch(url)
  .then((res)=>{
    if (res.status==404) {
      setIstrue(false);
    }
    else{
      setIstrue(true);

    }
  })
  .catch((err)=>{
    setIstrue(false);

  })
  },[])
  
  return(
    <>
    {istrue ? <ImageBackground
      source={  {uri: url}  }
      style={styles.bg}
    />:<ImageBackground
    source={   {uri:'https://ecpmarket.mywheels.pk/assets/images/noimage.png'}  }
    style={styles.bg}
  /> }
    </>
  );
  
}


const BasicTile = ({
  name,
  price,
  sold =null,
  photo =null,
  style,
  parentMargin,
  size,
  onPress,
  rating=null,
  numberOfReviews =null,
  previous_price,
}) => {
  const width = size - scale(parentMargin) - scale(parentMargin / 2);

  const [path,setParh ]=React.useState(`https://ecpmarket.mywheels.pk/assets/images/products/${photo}`) ;

  return (
    <Card style={StyleSheet.flatten([
      {
        width,
        marginTop: scale(parentMargin),
      },
      style])}
    >
      <TouchableOpacity
        style={styles.container}
        onPress={onPress}
      >
        <View
          style={{
            width,
            aspectRatio: 1 / 1,
          }}
        >
          <CheckImageOnUrl url={path}/>
        </View>

        <View style={styles.info}>
          <Text numberOfLines={2} weight="medium">{name}</Text>
          {/* <View style={styles.rating}>
            <Rating rating={rating} total={numberOfReviews} size={12} />
          </View> */}
          <View style={[styles.row, styles.between]}>
            <View style={styles.row}>
              <Text color="tertiary" weight="medium">{price}</Text>
              <Text color="gray50" style={styles.discount}>{previous_price}</Text>
            </View>
            {/* <Text color="gray50">{`${sold} sold`}</Text> */}
          </View>
        </View>
      </TouchableOpacity>
    </Card>
  );
};

BasicTile.propTypes = {
  name: PropTypes.string.isRequired,
  price: PropTypes.string,
  sold: PropTypes.number,
  images: PropTypes.array,
  rating: PropTypes.number,
  numberOfReviews: PropTypes.number,
  style: PropTypes.any,
  parentMargin: PropTypes.number,
  size: PropTypes.number,
  onPress: PropTypes.func,
  
  // beforeDiscount: PropTypes.string,
};

BasicTile.defaultProps = {
  style: null,
  parentMargin: 14,
  size: getScreenWidth() / 2,
  onPress: null,
  beforeDiscount: null,
};

export default BasicTile;
